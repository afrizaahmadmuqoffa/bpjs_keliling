<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public static function send($phone, $message)
    {
        $phone = self::formatPhone($phone);

        
        return Http::withHeaders([
            'Authorization' => env('FONNTE_TOKEN'),
        ])
        ->asForm()
        ->post(env('FONNTE_URL'), [
            'target' => $phone,
            'message' => $message,
        ]);
    }

    private static function formatPhone($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '62')) {
            return '62' . $phone;
        }

        return $phone;
    }
}
