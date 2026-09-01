<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DarajaService
{
    public function disburse(float $amount, string $phoneNumber): array
    {
        $consumerKey = config('services.daraja.consumer_key');
        $consumerSecret = config('services.daraja.consumer_secret');

        if (!$consumerKey || !$consumerSecret) {
            $fakeId = 'SIMULATED-' . strtoupper(Str::random(10));
            Log::info("Daraja disbursement simulated: KES {$amount} to {$phoneNumber}, ref {$fakeId}");
            return ['success' => true, 'transaction_id' => $fakeId, 'simulated' => true];
        }

        try {
            $tokenResponse = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
            $accessToken = $tokenResponse->json('access_token');

            $response = Http::withToken($accessToken)->post('https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest', [
                'InitiatorName' => config('services.daraja.initiator_name'),
                'SecurityCredential' => config('services.daraja.security_credential'),
                'CommandID' => 'BusinessPayment',
                'Amount' => $amount,
                'PartyA' => config('services.daraja.shortcode'),
                'PartyB' => $phoneNumber,
                'Remarks' => 'NEXUS capital disbursement',
                'QueueTimeOutURL' => config('services.daraja.timeout_url'),
                'ResultURL' => config('services.daraja.result_url'),
                'Occasion' => 'NEXUS',
            ]);

            $data = $response->json();
            return [
                'success' => $response->successful(),
                'transaction_id' => $data['ConversationID'] ?? null,
                'simulated' => false,
            ];
        } catch (\Exception $e) {
            Log::error('Daraja disbursement failed: ' . $e->getMessage());
            return ['success' => false, 'transaction_id' => null, 'simulated' => false];
        }
    }
}