<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(User $user, string $message): bool
    {
        $username = config('services.africastalking.username');
        $apiKey = config('services.africastalking.api_key');

        if (!$username || !$apiKey) {
            Log::info("SMS (simulated — no Africa's Talking credentials yet) to {$user->phone_number}: {$message}");
            Notification::create([
                'user_id' => $user->id,
                'message' => $message,
                'channel' => 'sms',
                'status' => 'simulated',
            ]);
            return true;
        }

        try {
            $response = Http::asForm()->withHeaders([
                'apiKey' => $apiKey,
                'Accept' => 'application/json',
            ])->post('https://api.africastalking.com/version1/messaging', [
                'username' => $username,
                'to' => $user->phone_number,
                'message' => $message,
            ]);

            $success = $response->successful();
            Notification::create([
                'user_id' => $user->id,
                'message' => $message,
                'channel' => 'sms',
                'status' => $success ? 'sent' : 'failed',
            ]);
            return $success;
        } catch (\Exception $e) {
            Log::error('SMS send failed: ' . $e->getMessage());
            return false;
        }
    }
}