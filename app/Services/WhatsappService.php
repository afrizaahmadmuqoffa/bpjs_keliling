<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public static function send($phone, $message)
    {
        $phone = self::formatPhone($phone);

        
        Http::withHeaders([
            'Authorization' => env('WABLAS_TOKEN'),
        ])->post('https://sby.wablas.com/api/send-message', [
            'phone'   => $phone,
            'message' => $message,
        ]);

        // if (!$response->successful()) {
        //     throw new \Exception('Wablas error: ' . $response->body());
        // }

        // return $response->json();
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
