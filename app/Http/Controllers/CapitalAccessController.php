<?php

namespace App\Http\Controllers;

use App\Models\CapitalAccess;
use App\Services\DarajaService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $applications = CapitalAccess::with(['user', 'interests'])->latest()->get();
        return view('capital.index', compact('applications'));
    }

    public function approve(CapitalAccess $capitalAccess)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $capitalAccess->status = 'approved';
        $capitalAccess->save();

        (new SmsService())->send($capitalAccess->user, "NEXUS: Your application for KES {$capitalAccess->amount_requested} has been approved and is pending disbursement.");

        return back()->with('status', 'Application approved.');
    }

    public function reject(CapitalAccess $capitalAccess)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $capitalAccess->status = 'rejected';
        $capitalAccess->save();

        (new SmsService())->send($capitalAccess->user, "NEXUS: Your application for KES {$capitalAccess->amount_requested} was not approved at this time.");

        return back()->with('status', 'Application rejected.');
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

    public function darajaCallback(Request $request)
    {
        Log::info('Daraja callback received: ' . json_encode($request->all()));

        $conversationId = $request->input('Result.ConversationID') ?? $request->input('ConversationID');

        if ($conversationId) {
            $capitalAccess = CapitalAccess::where('daraja_transaction_id', $conversationId)->first();
            if ($capitalAccess) {
                $resultCode = $request->input('Result.ResultCode');
                $capitalAccess->status = (in_array($resultCode, [0, '0'], true)) ? 'disbursed' : 'approved';
                $capitalAccess->save();
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }
}