<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SendOtpMail extends Mailable
{
    public string $otp;

    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this
            ->subject('Kode OTP Reset Password')
            ->view('emails.otp')
            ->with([
                'otp' => $this->otp
            ]);
    }
}