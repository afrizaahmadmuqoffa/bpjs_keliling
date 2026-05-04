<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\PasswordResetService;

class ForgotPassword extends Component
{
    public string $email = '';
    public string $otp = '';
    public string $password = '';
    public string $password_confirmation = '';

    public int $step = 1; // 1: email, 2: OTP, 3: new password
    public string $resetToken = '';

    public function sendOtp()
    {
        $this->validate([
            'email' => 'required|email'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid'
        ]);

        try {
            $service = new PasswordResetService();
            $service->sendOtp($this->email);

            $this->step = 2;
            session()->flash('success', 'Kode OTP telah dikirim ke email Anda. Periksa inbox/spam.');
        } catch (\Exception $e) {
            $this->addError('email', $e->getMessage());
        }
    }

    public function verifyOtp()
    {
        $this->validate([
            'otp' => 'required|digits:6'
        ], [
            'otp.required' => 'Kode OTP wajib diisi',
            'otp.digits' => 'Kode OTP harus 6 digit'
        ]);

        try {
            $service = new PasswordResetService();
            $this->resetToken = $service->verifyOtp($this->email, $this->otp);

            $this->step = 3;
            session()->flash('success', 'OTP berhasil diverifikasi. Silakan masukkan password baru.');
        } catch (\Exception $e) {
            $this->addError('otp', $e->getMessage());
        }
    }

    public function resetPassword()
    {
        $this->validate([
            'password' => 'required|min:6|confirmed'
        ], [
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok'
        ]);

        try {
            $service = new PasswordResetService();
            $service->resetPassword($this->email, $this->resetToken, $this->password);

            session()->flash('success', 'Password berhasil direset. Silakan login dengan password baru.');
            return redirect()->route('login');
        } catch (\Exception $e) {
            $this->addError('password', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.forgot-password');
    }
}
