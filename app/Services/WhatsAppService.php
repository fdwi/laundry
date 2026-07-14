<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message using Fonnte API or log if not configured.
     *
     * @param string $phone
     * @param string $message
     * @return bool
     */
    public static function sendMessage(string $phone, string $message): bool
    {
        $token = env('FONNTE_TOKEN');

        if (empty($token)) {
            // Fallback to logging if token is not set
            Log::info("WhatsApp Message to {$phone}: {$message}");
            return true;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->withoutVerifying()->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62', // Default to Indonesia
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['status']) && $result['status'] === true) {
                    return true;
                }
                Log::error('Fonnte API Reject: ' . json_encode($result));
                return false;
            }

            Log::error('Fonnte HTTP Error: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('WhatsAppService Exception: ' . $e->getMessage());
            return false;
        }
    }
}
