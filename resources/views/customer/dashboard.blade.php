@extends('layouts.app')

@section('title', 'My Dashboard – LoanApp')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Welcome back, {{ auth()->user()->name }}!</h4>
        <small class="text-muted">Here's a summary of your loan activity</small>
    </div>
    <a href="{{ route('customer.loans.create') }}" class="btn btn-primary">Apply for a Loan</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card blue p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Applications</div>
                    <div class="fs-2 fw-bold text-primary">{{ $totalLoans }}</div>
                </div>
                <span class="fs-1 text-primary opacity-25">&#128196;</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card green p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Active Loans</div>
                    <div class="fs-2 fw-bold text-success">{{ $activeLoans }}</div>
                </div>
                <span class="fs-1 text-success opacity-25">&#9989;</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card yellow p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Pending Review</div>
                    <div class="fs-2 fw-bold text-warning">{{ $pendingLoans }}</div>
                </div>
                <span class="fs-1 text-warning opacity-25">&#9203;</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0 fw-semibold">Recent Loan Applications</h6>
        <a href="{{ route('customer.loans.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>
    <div class="card-body p-0">
        @if($recentLoans->isEmpty())
            <div class="text-center py-5 text-muted">
                <p class="mb-1">You haven't applied for any loans yet.</p>
                <a href="{{ route('customer.loans.create') }}" class="btn btn-primary btn-sm mt-2">Apply Now</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Loan #</th>
                            <th>Amount</th>
                            <th>Tenure</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Applied On</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLoans as $loan)
                            <tr>
                                <td class="fw-medium">#{{ $loan->id }}</td>
                                <td>₹{{ number_format($loan->amount, 2) }}</td>
                                <td>{{ $loan->tenure_months }} months</td>
                                <td>{{ Str::limit($loan->purpose, 30) }}</td>
                                <td>
                                    <span class="badge badge-{{ $loan->status }}">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                </td>
                                <td>{{ $loan->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('customer.loans.show', $loan->id) }}"
                                       class="btn btn-sm btn-outline-primary">View</a>
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
