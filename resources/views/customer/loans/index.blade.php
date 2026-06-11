@extends('layouts.app')

@section('title', 'My Loans – LoanApp')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">My Loan Applications</h4>
    <a href="{{ route('customer.loans.create') }}" class="btn btn-primary">Apply for Loan</a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($loans->isEmpty())
            <div class="text-center py-5 text-muted">
                <p>No loan applications found.</p>
                <a href="{{ route('customer.loans.create') }}" class="btn btn-primary btn-sm">Apply Now</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Loan #</th>
                            <th>Amount</th>
                            <th>Tenure</th>
                            <th>Interest Rate</th>
                            <th>Monthly EMI</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Date Applied</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $loan)
                            <tr>
                                <td class="fw-medium">#{{ $loan->id }}</td>
                                <td>₹{{ number_format($loan->amount, 2) }}</td>
                                <td>{{ $loan->tenure_months }} mo.</td>
                                <td>
                                    @if($loan->interest_rate !== null)
                                        {{ $loan->interest_rate }}% p.a.
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($loan->monthly_emi !== null)
                                        ₹{{ number_format($loan->monthly_emi, 2) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($loan->purpose, 25) }}</td>
                                <td>
                                    <span class="badge badge-{{ $loan->status }}">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                </td>
                                <td>{{ $loan->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('customer.loans.show', $loan->id) }}"
                                       class="btn btn-sm btn-outline-primary">Details</a>
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
