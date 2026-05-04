<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetService
{
    // Send OTP
    public function sendOtp(string $email): void
    {
        $user = User::where('email', $email)->first();

        $record = DB::table('password_resets')->where('email', $email)->first();

        if ($record && Carbon::parse($record->updated_at)->addSeconds(60)->gt(now())) {
            throw new \Exception('RATE_LIMIT');
        }

        $otp = random_int(100000, 999999);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make((string)$otp),
                'attempts' => 0,
                'reset_token' => null,
                'verified_at' => null,
                'expires_at' => now()->addMinutes(5),
                'updated_at' => now(),
                'created_at' => $record->created_at ?? now(),
            ]
        );
        if ($user) {
            Mail::to($email)->send(new \App\Mail\SendOtpMail((string)$otp));
            return;
        }
    }

    // Verify OTP
    public function verifyOtp(string $email, string $otp): string
    {
        $record = DB::table('password_resets')->where('email', $email)->first();

        if (!$record) {
            throw new \Exception('OTP tidak valid atau sudah kadaluarsa');
        }

        // Expired Logic
        if (Carbon::parse($record->expires_at)->isPast()) {
            DB::table('password_resets')->where('email', $email)->delete();
            throw new \Exception('OTP sudah kadaluarsa');
        }

        // Anti Brute Force Logic
        if ($record->attempts >= 5) {
            DB::table('password_resets')->where('email', $email)->delete();
            throw new \Exception('Terlalu banyak percobaan. Silakan minta OTP baru.');
        }

        // OTP Validation
        if (!Hash::check($otp, $record->token)) {
            DB::table('password_resets')
                ->where('email', $email)
                ->increment('attempts');

            throw new \Exception('OTP tidak valid');
        }

        // Generate Reset Token
        $resetToken = Str::random(64);

        DB::table('password_resets')
            ->where('email', $email)
            ->update([
                'reset_token' => Hash::make($resetToken),
                'verified_at' => now(),
                'updated_at' => now(),
            ]);

        return $resetToken;
    }

    // Reset Password
    public function resetPassword(string $email, string $resetToken, string $password): void
    {
        $record = DB::table('password_resets')->where('email', $email)->first();

        if (!$record || !$record->reset_token) {
            throw new \Exception('Token tidak valid');
        }

        // Expired Logic
        if (
            !$record->verified_at ||
            Carbon::parse($record->verified_at)->addMinutes(10)->isPast()
        ) {
            DB::table('password_resets')->where('email', $email)->delete();
            throw new \Exception('Token sudah kadaluarsa. Silakan ulangi proses reset password.');
        }

        if (!Hash::check($resetToken, $record->reset_token)) {
            throw new \Exception('Token tidak valid');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            DB::table('password_resets')->where('email', $email)->delete();
            throw new \Exception('User tidak ditemukan');
        }

        DB::transaction(function () use ($user, $email, $password) {
            $user->update([
                'password' => Hash::make($password)
            ]);

            DB::table('password_resets')->where('email', $email)->delete();
        });
    }
}
