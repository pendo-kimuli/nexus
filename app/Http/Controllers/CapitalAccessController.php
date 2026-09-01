<?php

namespace App\Http\Controllers;

use App\Models\CapitalAccess;
use App\Services\DarajaService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CapitalAccessController extends Controller
{
    public function create()
    {
        $trustScore = Auth::user()->trustScore;
        $threshold = config('nexus.trust_threshold', 70);

        if (!$trustScore || $trustScore->score < $threshold) {
            return redirect()->route('dashboard')->with('status', "You need a trust score of at least {$threshold} to apply. Your current score is " . ($trustScore->score ?? 0) . '.');
        }

        return view('capital.create', compact('threshold'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount_requested' => 'required|numeric|min:1',
            'purpose' => 'required|string',
        ]);
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        CapitalAccess::create($validated);

        return redirect()->route('dashboard')->with('status', 'Capital access application submitted.');
    }

    public function index()
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $applications = CapitalAccess::with('user')->latest()->get();
        return view('capital.index', compact('applications'));
    }

    public function approve(CapitalAccess $capitalAccess)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $capitalAccess->status = 'approved';
        $capitalAccess->save();
        return back()->with('status', 'Application approved.');
    }

    public function disburse(CapitalAccess $capitalAccess, DarajaService $daraja)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $result = $daraja->disburse($capitalAccess->amount_requested, $capitalAccess->user->phone_number);

        if ($result['success']) {
            $capitalAccess->status = 'disbursed';
            $capitalAccess->daraja_transaction_id = $result['transaction_id'];
            $capitalAccess->disbursed_at = now();
            $capitalAccess->save();

            (new SmsService())->send($capitalAccess->user, "NEXUS: Your disbursement of KES {$capitalAccess->amount_requested} has been processed.");

            return back()->with('status', 'Disbursed' . ($result['simulated'] ? ' (simulated — add Daraja credentials to go live).' : '.'));
        }

        return back()->with('status', 'Disbursement failed. Check logs.');
    }
}