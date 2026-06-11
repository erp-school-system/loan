@extends('layouts.app')

@section('title', 'Admin Dashboard – LoanApp')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Admin Dashboard</h4>
        <small class="text-muted">Overview of all loan applications</small>
    </div>
    <a href="{{ route('admin.loans.index') }}?status=pending" class="btn btn-warning">
        Review Pending ({{ $pendingLoans }})
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card blue p-3">
            <div class="text-muted small mb-1">Total Applications</div>
            <div class="fs-2 fw-bold text-primary">{{ $totalApplications }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card yellow p-3">
            <div class="text-muted small mb-1">Pending Review</div>
            <div class="fs-2 fw-bold text-warning">{{ $pendingLoans }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card green p-3">
            <div class="text-muted small mb-1">Approved</div>
            <div class="fs-2 fw-bold text-success">{{ $approvedLoans }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card red p-3">
            <div class="text-muted small mb-1">Rejected</div>
            <div class="fs-2 fw-bold text-danger">{{ $rejectedLoans }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0 fw-semibold">Recent Applications</h6>
        <a href="{{ route('admin.loans.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>
    <div class="card-body p-0">
        @if($recentLoans->isEmpty())
            <div class="text-center py-5 text-muted">No loan applications yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Loan #</th>
                            <th>Applicant</th>
                            <th>Amount</th>
                            <th>Tenure</th>
                            <th>Status</th>
                            <th>Applied On</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLoans as $loan)
                            <tr>
                                <td class="fw-medium">#{{ $loan->id }}</td>
                                <td>
                                    <div>{{ $loan->user->name }}</div>
                                    <small class="text-muted">{{ $loan->user->email }}</small>
                                </td>
                                <td>₹{{ number_format($loan->amount, 2) }}</td>
                                <td>{{ $loan->tenure_months }} mo.</td>
                                <td>
                                    <span class="badge badge-{{ $loan->status }}">{{ ucfirst($loan->status) }}</span>
                                </td>
                                <td>{{ $loan->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.loans.show', $loan->id) }}"
                                       class="btn btn-sm btn-outline-primary">Review</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
