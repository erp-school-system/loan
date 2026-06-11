@extends('layouts.app')

@section('title', 'Apply for a Loan – LoanApp')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="mb-4">
            <a href="{{ route('customer.loans.index') }}" class="text-decoration-none text-muted">
                &larr; Back to My Loans
            </a>
            <h4 class="fw-bold mt-2 mb-0">Apply for a Loan</h4>
            <p class="text-muted">Fill in the details below. Your application will be reviewed within 1-2 business days.</p>
        </div>

        <div class="card p-4">
            <form method="POST" action="{{ route('customer.loans.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="amount" class="form-label fw-medium">Loan Amount (₹)</label>
                    <input type="number" class="form-control @error('amount') is-invalid @enderror"
                           id="amount" name="amount" value="{{ old('amount') }}"
                           min="1000" max="10000000" step="100" placeholder="e.g. 50000" required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tenure_months" class="form-label fw-medium">Loan Tenure (Months)</label>
                    <input type="number" class="form-control @error('tenure_months') is-invalid @enderror"
                           id="tenure_months" name="tenure_months" value="{{ old('tenure_months') }}"
                           min="1" max="360" placeholder="e.g. 24" required>
                    @error('tenure_months')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="purpose" class="form-label fw-medium">Purpose of Loan</label>
                    <textarea class="form-control @error('purpose') is-invalid @enderror"
                              id="purpose" name="purpose" rows="4"
                              placeholder="Describe why you need this loan..."
                              required>{{ old('purpose') }}</textarea>
                    @error('purpose')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if($errors->has('amount') && str_contains($errors->first('amount'), 'active'))
                    <div class="alert alert-warning small">
                        {{ $errors->first('amount') }}
                    </div>
                @endif

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-4">Submit Application</button>
                    <a href="{{ route('customer.loans.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>

        
    </div>
</div>
@endsection
