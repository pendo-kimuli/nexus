<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DarajaService
{
    // Converts 0712345678 or +254712345678 into the 254712345678 format Safaricom's API requires.
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '0')) {
            $digits = '254' . substr($digits, 1);
        }
        return $digits;
    }

    public function disburse(float $amount, string $phoneNumber): array
    {
        $consumerKey = config('services.daraja.consumer_key');
        $consumerSecret = config('services.daraja.consumer_secret');

        if (!$consumerKey || !$consumerSecret) {
            $fakeId = 'SIMULATED-' . strtoupper(Str::random(10));
            Log::info("Daraja disbursement simulated: KES {$amount} to {$phoneNumber}, ref {$fakeId}");
            return ['success' => true, 'transaction_id' => $fakeId, 'simulated' => true];
        }

        $partyB = $this->normalizePhone($phoneNumber);

        try {
            $tokenResponse = Http::withHeaders(['User-Agent' => 'NEXUS/1.0'])
                ->withBasicAuth($consumerKey, $consumerSecret)
                ->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

            $accessToken = $tokenResponse->json('access_token');

            if (!$accessToken) {
                Log::error('Daraja: no access_token in response. Body: ' . $tokenResponse->body());
                return ['success' => false, 'transaction_id' => null, 'simulated' => false];
            }

            $response = Http::withHeaders(['User-Agent' => 'NEXUS/1.0'])
                ->withToken($accessToken)
                ->post('https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest', [
                    'InitiatorName' => config('services.daraja.initiator_name'),
                    'SecurityCredential' => config('services.daraja.security_credential'),
                    'CommandID' => 'BusinessPayment',
                    'Amount' => $amount,
                    'PartyA' => config('services.daraja.shortcode'),
                    'PartyB' => $partyB,
                    'Remarks' => 'NEXUS capital disbursement',
                    'QueueTimeOutURL' => config('services.daraja.timeout_url'),
                    'ResultURL' => config('services.daraja.result_url'),
                    'Occasion' => 'NEXUS',
                ]);

            Log::info('Daraja B2C status: ' . $response->status());
            Log::info('Daraja B2C body: ' . $response->body());

            $data = $response->json();
            return [
                'success' => $response->successful(),
                'transaction_id' => $data['ConversationID'] ?? null,
                'simulated' => false,
            ];
        } catch (\Exception $e) {
            Log::error('Daraja exception: ' . $e->getMessage());
            return ['success' => false, 'transaction_id' => null, 'simulated' => false];
        }
    }
}