<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Repayment;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $totalLoans   = $user->loans()->count();
        $activeLoans  = $user->loans()->where('status', 'approved')->count();
        $pendingLoans = $user->loans()->where('status', 'pending')->count();
        $recentLoans  = $user->loans()->latest()->take(5)->get();

        return view('customer.dashboard', compact('totalLoans', 'activeLoans', 'pendingLoans', 'recentLoans'));
    }

    public function index()
    {
        $loans = auth()->user()->loans()->latest()->paginate(10);

        return view('customer.loans.index', compact('loans'));
    }

    public function create()
    {
        return view('customer.loans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:1000|max:10000000',
            'tenure_months'  => 'required|integer|min:1|max:360',
            'purpose'        => 'required|string|min:10|max:1000',
        ]);

        $activePending = auth()->user()->loans()
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        if ($activePending >= 3) {
            return back()
                ->withErrors(['amount' => 'You cannot have more than 3 active or pending loan applications.'])
                ->withInput();
        }

        Loan::create([
            'user_id'       => auth()->id(),
            'amount'        => $request->amount,
            'tenure_months' => $request->tenure_months,
            'purpose'       => $request->purpose,
            'status'        => 'pending',
        ]);

        return redirect()->route('customer.loans.index')
            ->with('success', 'Loan application submitted. We will review it shortly.');
    }

    public function show(int $id)
    {
        $loan = Loan::where('user_id', auth()->id())->findOrFail($id);
        $repayments = $loan->repayments()->orderBy('installment_no')->get();

        return view('customer.loans.show', compact('loan', 'repayments'));
    }

    public function payInstallment(Request $request, int $loanId, int $repaymentId)
    {
        $loan = Loan::where('user_id', auth()->id())
            ->where('status', 'approved')
            ->findOrFail($loanId);

        $repayment = Repayment::where('loan_id', $loan->id)
            ->whereIn('status', ['pending', 'partial'])
            ->findOrFail($repaymentId);

        $repayment->update([
            'paid_amount' => $repayment->due_amount,
            'paid_at'     => now(),
            'status'      => 'paid',
        ]);

        return back()->with('success', 'Payment of ₹' . number_format($repayment->due_amount, 2) . ' recorded for installment #' . $repayment->installment_no . '.');
    }
}
