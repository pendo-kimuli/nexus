<?php

namespace App\Http\Controllers;

use App\Models\CapitalAccess;
use App\Models\CapitalAccessInterest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class InvestorController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->isInvestor(), 403);

        $eligibleUsers = User::where('role', 'individual')
            ->whereHas('trustScore', fn ($q) => $q->where('capital_eligible', true))
            ->with('trustScore')
            ->get();

        $applications = CapitalAccess::whereIn('status', ['pending', 'approved'])
            ->with(['user', 'interests'])->latest()->get();

        return view('investors.index', compact('eligibleUsers', 'applications'));
    }

    public function expressInterest(CapitalAccess $capitalAccess)
    {
        abort_unless(Auth::user()->isInvestor(), 403);

        CapitalAccessInterest::firstOrCreate([
            'capital_access_id' => $capitalAccess->id,
            'investor_id' => Auth::id(),
        ]);

        return back()->with('status', 'Interest recorded.');
    }
}