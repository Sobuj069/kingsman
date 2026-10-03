<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchSelectionController extends Controller
{
    /**
     * Show the branch selection interface.
     */
    public function index()
    {
        // Only allow Admins (Role ID 1) or Super Admins to manually select branches
        if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('dashboard');
        }

        // If user already has a branch selected, redirect to dashboard
        if (session()->has('branch_id')) {
            return redirect()->route('dashboard');
        }

        // Fetch all branches
        $branches = Branch::all();

        return view('backend.pages.branch.select', compact('branches'));
    }

    /**
     * Set the selected branch in session.
     */
    public function select(Request $request)
    {
        // Security check: only Admins or Super Admins can set/switch branches manually
        if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);

        // Save branch ID and Name in session
        $branch = Branch::find($request->branch_id);
        session(['branch_id' => $branch->id]);
        session(['branch_name' => $branch->name]);
        
        // Also set branch_filter_id if your existing system uses it
        session(['branch_filter_id' => $branch->id]);

        return redirect()->route('dashboard')->with('success', 'Branch selected successfully!');
    }
}
