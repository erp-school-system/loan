@extends('layouts.app')

@section('title', 'Loan Applications – LoanApp Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Loan Applications</h4>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('admin.loans.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-medium mb-1">Filter by Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-medium mb-1">Search by Applicant</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                <a href="{{ route('admin.loans.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($loans->isEmpty())
            <div class="text-center py-5 text-muted">No loan applications found.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Loan #</th>
                            <th>Applicant</th>
                            <th>Amount</th>
                            <th>Tenure</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Applied On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $loan)
                            <tr>
                                <td class="fw-medium">#{{ $loan->id }}</td>
                                <td>
                                    <div class="fw-medium">{{ $loan->user->name }}</div>
                                    <small class="text-muted">{{ $loan->user->email }}</small>
                                </td>
                                <td>₹{{ number_format($loan->amount, 2) }}</td>
                                <td>{{ $loan->tenure_months }} mo.</td>
                                <td>{{ Str::limit($loan->purpose, 30) }}</td>
                                <td>
                                    <span class="badge badge-{{ $loan->status }}">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                </td>
                                <td>{{ $loan->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.loans.show', $loan->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        {{ $loan->status === 'pending' ? 'Review' : 'View' }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $loans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
