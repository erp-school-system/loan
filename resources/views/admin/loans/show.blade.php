@extends('layouts.app')

@section('title')Loan #{{ $loan->id }} – Admin Review @endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.loans.index') }}" class="text-decoration-none text-muted">
        &larr; Back to Applications
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
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Applicant Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Name</td>
                        <td class="fw-medium">{{ $loan->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $loan->user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Phone</td>
                        <td>{{ $loan->user->phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Address</td>
                        <td>{{ $loan->user->address ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Member Since</td>
                        <td>{{ $loan->user->created_at->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total Loans</td>
                        <td>{{ $loan->user->loans->count() }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Loan Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Amount Requested</td>
                        <td class="fw-medium fs-5 text-primary">₹{{ number_format($loan->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tenure</td>
                        <td>{{ $loan->tenure_months }} months</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Applied On</td>
                        <td>{{ $loan->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                    @if($loan->status !== 'pending')
                        <tr>
                            <td class="text-muted">Decision On</td>
                            <td>{{ $loan->approved_at?->format('d M Y') }}</td>
                        </tr>
                        @if($loan->approvedBy)
                            <tr>
                                <td class="text-muted">Decided By</td>
                                <td>{{ $loan->approvedBy->name }}</td>
                            </tr>
                        @endif
                        @if($loan->interest_rate !== null)
                            <tr>
                                <td class="text-muted">Interest Rate</td>
                                <td>{{ $loan->interest_rate }}% p.a.</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Monthly EMI</td>
                                <td class="fw-medium text-success">₹{{ number_format($loan->monthly_emi, 2) }}</td>
                            </tr>
                        @endif
                    @endif
                </table>
                <div class="mt-3">
                    <div class="text-muted small fw-medium mb-1">Purpose of Loan</div>
                    <p class="mb-0 p-2 bg-light rounded small">{{ $loan->purpose }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        @if($loan->status === 'pending')
            <div class="card mb-3 border-success">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="mb-0 fw-semibold">Approve this Loan</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.loans.approve', $loan->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="interest_rate" class="form-label fw-medium">
                                Annual Interest Rate (%)
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('interest_rate') is-invalid @enderror"
                                       id="interest_rate" name="interest_rate"
                                       value="{{ old('interest_rate', '10') }}"
                                       min="0" max="100" step="0.01" required>
                                <span class="input-group-text">% p.a.</span>
                                @error('interest_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text" id="emiPreview"></div>
                        </div>
                        <div class="mb-3">
                            <label for="admin_notes_approve" class="form-label fw-medium">
                                Notes (optional)
                            </label>
                            <textarea class="form-control" id="admin_notes_approve" name="admin_notes"
                                      rows="2" placeholder="Any conditions or remarks...">{{ old('admin_notes') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success px-4"
                                onclick="return confirm('Approve this loan application?')">
                            Approve Loan
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-danger">
                <div class="card-header bg-danger text-white py-3">
                    <h6 class="mb-0 fw-semibold">Reject this Application</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.loans.reject', $loan->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="admin_notes_reject" class="form-label fw-medium">
                                Reason for Rejection <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('admin_notes') is-invalid @enderror"
                                      id="admin_notes_reject" name="admin_notes" rows="3"
                                      placeholder="Provide a clear reason..." required>{{ old('admin_notes') }}</textarea>
                            @error('admin_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger px-4"
                                onclick="return confirm('Reject this loan application? This cannot be undone.')">
                            Reject Application
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-semibold">Decision Summary</h6>
                </div>
                <div class="card-body">
                    @if($loan->status === 'approved')
                        <div class="row g-3 text-center mb-3">
                            <div class="col-4">
                                <div class="border rounded p-3">
                                    <div class="text-muted small mb-1">Interest Rate</div>
                                    <div class="fw-bold fs-5 text-primary">{{ $loan->interest_rate }}%</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-3">
                                    <div class="text-muted small mb-1">Monthly EMI</div>
                                    <div class="fw-bold fs-5 text-success">₹{{ number_format($loan->monthly_emi, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-3">
                                    <div class="text-muted small mb-1">Total Payable</div>
                                    <div class="fw-bold fs-5">₹{{ number_format($loan->total_payable, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($loan->admin_notes)
                        <div class="alert {{ $loan->status === 'approved' ? 'alert-info' : 'alert-danger' }} mb-0">
                            <strong>Admin Notes:</strong> {{ $loan->admin_notes }}
                        </div>
                    @endif
                </div>
            </div>

            @if($loan->status === 'approved' && $loan->repayments->isNotEmpty())
                <div class="card mt-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between">
                        <h6 class="mb-0 fw-semibold">Repayment Schedule</h6>
                        <span class="text-muted small">
                            {{ $loan->paid_installments_count }}/{{ $loan->tenure_months }} paid
                        </span>
                    </div>
                    <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>#</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Paid On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loan->repayments as $rep)
                                    <tr class="{{ $rep->status === 'paid' ? 'table-success' : '' }}">
                                        <td>{{ $rep->installment_no }}</td>
                                        <td>{{ $rep->due_date->format('d M Y') }}</td>
                                        <td>₹{{ number_format($rep->due_amount, 2) }}</td>
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    const principal = {{ (float) $loan->amount }};
    const tenure    = {{ $loan->tenure_months }};

    function calcEmi(rate) {
        if (rate == 0) return (principal / tenure).toFixed(2);
        const r = rate / 12 / 100;
        const emi = principal * r * Math.pow(1 + r, tenure) / (Math.pow(1 + r, tenure) - 1);
        return emi.toFixed(2);
    }

    const rateInput = document.getElementById('interest_rate');
    const preview   = document.getElementById('emiPreview');

    if (rateInput && preview) {
        function updatePreview() {
            const rate = parseFloat(rateInput.value) || 0;
            const emi  = calcEmi(rate);
            const total = (emi * tenure).toFixed(2);
            preview.textContent = `Estimated EMI: ₹${parseFloat(emi).toLocaleString('en-IN', {minimumFractionDigits: 2})} / month  |  Total Payable: ₹${parseFloat(total).toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
        }
        rateInput.addEventListener('input', updatePreview);
        updatePreview();
    }
</script>
@endsection
