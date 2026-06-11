@extends('layouts.app')

@section('title')Loan #{{ $loan->id }} – LoanApp @endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('customer.loans.index') }}" class="text-decoration-none text-muted">
        &larr; Back to My Loans
    </a>
    <div class="d-flex justify-content-between align-items-start mt-2">
        <h4 class="fw-bold mb-0">Loan Application #{{ $loan->id }}</h4>
        <span class="badge badge-{{ $loan->status }} fs-6 py-2 px-3">
            {{ ucfirst($loan->status) }}
        </span>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Loan Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:45%">Applied Amount</td>
                        <td class="fw-medium">₹{{ number_format($loan->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tenure</td>
                        <td>{{ $loan->tenure_months }} months</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Purpose</td>
                        <td>{{ $loan->purpose }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Applied On</td>
                        <td>{{ $loan->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                    @if($loan->status !== 'pending')
                        <tr>
                            <td class="text-muted">Decision Date</td>
                            <td>{{ $loan->approved_at?->format('d M Y') }}</td>
                        </tr>
                        @if($loan->approvedBy)
                            <tr>
                                <td class="text-muted">Reviewed By</td>
                                <td>{{ $loan->approvedBy->name }}</td>
                            </tr>
                        @endif
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        @if($loan->status === 'approved')
            <div class="card h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-semibold">Approved Loan Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <div class="text-muted small mb-1">Interest Rate</div>
                                <div class="fw-bold fs-5 text-primary">{{ $loan->interest_rate }}%</div>
                                <div class="text-muted" style="font-size:11px">per annum</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <div class="text-muted small mb-1">Monthly EMI</div>
                                <div class="fw-bold fs-5 text-success">₹{{ number_format($loan->monthly_emi, 2) }}</div>
                                <div class="text-muted" style="font-size:11px">per month</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <div class="text-muted small mb-1">Total Payable</div>
                                <div class="fw-bold fs-5 text-danger">₹{{ number_format($loan->total_payable, 2) }}</div>
                                <div class="text-muted" style="font-size:11px">incl. interest</div>
                            </div>
                        </div>
                    </div>
                    @if($loan->admin_notes)
                        <div class="alert alert-info mt-3 mb-0 small">
                            <strong>Note from admin:</strong> {{ $loan->admin_notes }}
                        </div>
                    @endif
                    <div class="mt-3">
                        <div class="d-flex justify-content-between text-muted small mb-1">
                            <span>Repayment Progress</span>
                            <span>{{ $loan->paid_installments_count }} / {{ $loan->tenure_months }} installments paid</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @php
                                $pct = $loan->tenure_months > 0
                                    ? round(($loan->paid_installments_count / $loan->tenure_months) * 100)
                                    : 0;
                            @endphp
                            <div class="progress-bar bg-success" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($loan->status === 'rejected')
            <div class="card h-100 border-danger">
                <div class="card-header bg-danger text-white py-3">
                    <h6 class="mb-0 fw-semibold">Application Rejected</h6>
                </div>
                <div class="card-body">
                    @if($loan->admin_notes)
                        <p class="mb-0"><strong>Reason:</strong> {{ $loan->admin_notes }}</p>
                    @else
                        <p class="text-muted mb-0">No reason provided.</p>
                    @endif
                </div>
            </div>
        @else
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-center text-center">
                    <div>
                        <div class="fs-1 mb-2">&#9203;</div>
                        <h6 class="fw-semibold text-warning">Under Review</h6>
                        <p class="text-muted mb-0">Your application is being reviewed by our team.<br>
                        You will see the decision here once it's processed.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@if($loan->status === 'approved' && $repayments->isNotEmpty())
    <div class="card mt-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-semibold">Repayment Schedule</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Due Date</th>
                            <th>Amount Due</th>
                            <th>Amount Paid</th>
                            <th>Status</th>
                            <th>Paid On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($repayments as $rep)
                            <tr class="{{ $rep->status === 'paid' ? 'table-success' : ($rep->due_date->isPast() && $rep->status !== 'paid' ? 'table-danger' : '') }}">
                                <td class="fw-medium">{{ $rep->installment_no }}</td>
                                <td>{{ $rep->due_date->format('d M Y') }}</td>
                                <td>₹{{ number_format($rep->due_amount, 2) }}</td>
                                <td>
                                    @if($rep->paid_amount > 0)
                                        ₹{{ number_format($rep->paid_amount, 2) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rep->status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($rep->due_date->isPast())
                                        <span class="badge bg-danger">Overdue</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $rep->paid_at ? $rep->paid_at->format('d M Y') : '—' }}</td>
                                <td>
                                    @if($rep->status !== 'paid')
                                        <form method="POST"
                                              action="{{ route('customer.repayments.pay', [$loan->id, $rep->id]) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success"
                                                    onclick="return confirm('Mark installment #{{ $rep->installment_no }} as paid?')">
                                                Pay Now
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">Done</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
