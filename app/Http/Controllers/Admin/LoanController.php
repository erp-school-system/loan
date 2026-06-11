<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function dashboard()
    {
        $totalApplications = Loan::count();
        $pendingLoans      = Loan::where('status', 'pending')->count();
        $approvedLoans     = Loan::where('status', 'approved')->count();
        $rejectedLoans     = Loan::where('status', 'rejected')->count();
        $recentLoans       = Loan::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalApplications',
            'pendingLoans',
            'approvedLoans',
            'rejectedLoans',
            'recentLoans'
        ));
    }

    public function index(Request $request)
    {
        $query = Loan::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $loans = $query->paginate(15)->withQueryString();

        return view('admin.loans.index', compact('loans'));
    }

    public function show(int $id)
    {
        $loan = Loan::with(['user', 'repayments', 'approvedBy'])->findOrFail($id);

        return view('admin.loans.show', compact('loan'));
    }

    public function approve(Request $request, int $id)
    {
        $request->validate([
            'interest_rate' => 'required|numeric|min:0|max:100',
            'admin_notes'   => 'nullable|string|max:1000',
        ]);

        $loan = Loan::where('status', 'pending')->findOrFail($id);

        $emi = $loan->calculateEmi((float) $request->interest_rate);

        $loan->update([
            'status'        => 'approved',
            'interest_rate' => $request->interest_rate,
            'monthly_emi'   => $emi,
            'admin_notes'   => $request->admin_notes,
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
        ]);

        $loan->generateRepaymentSchedule();

        return redirect()->route('admin.loans.show', $loan->id)
            ->with('success', 'Loan approved. Repayment schedule has been generated.');
    }

    public function reject(Request $request, int $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $loan = Loan::where('status', 'pending')->findOrFail($id);

        $loan->update([
            'status'      => 'rejected',
            'admin_notes' => $request->admin_notes,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.loans.index')
            ->with('success', 'Loan application has been rejected.');
    }
}
