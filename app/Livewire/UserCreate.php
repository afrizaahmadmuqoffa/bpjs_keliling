<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;

class UserCreate extends Component
{
    public string $nama     = '';
    public string $nik      = '';
    public string $email    = '';
    public string $no_hp    = '';
    public string $role     = 'pic';
    public string $password = '';

    public bool $showPassword = false;
    public bool $showSuccess = false;
    public string $successMessage = '';



    protected function rules(): array
    {
        $isSuperAdmin = Auth::user()?->role === 'super_admin';

        return [
            'nama' => [
                'required',
                'string',
                'min:1',
                'max:255',
                'regex:/^[a-zA-Z\s\.\']+$/'
            ],

            'nik' => [
                'required',
                'digits:16',
                Rule::unique('users', 'nik')
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
            ],

            'no_hp' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^(08|628)[0-9]+$/',
                Rule::unique('users', 'no_hp')
            ],

            'role' => [
                'required',
                Rule::in($isSuperAdmin ? ['super_admin', 'admin', 'pic'] : ['admin', 'pic'])
            ],

            'password' => [
                'required',
                'min:6',
                'max:100'
            ],
        ];
    }

    protected $messages = [

        // NAMA
        'nama.required' => 'Nama lengkap wajib diisi.',
        'nama.min'      => 'Nama minimal 1 karakter.',
        'nama.max'      => 'Nama maksimal 255 karakter.',
        'nama.regex'    => 'Nama hanya boleh huruf, spasi, titik, dan apostrof.',

        // NIK
        'nik.required' => 'NIK wajib diisi.',
        'nik.digits'   => 'NIK harus terdiri dari 16 digit angka.',
        'nik.unique'   => 'NIK sudah terdaftar.',

        // EMAIL
        'email.required' => 'Email wajib diisi.',
        'email.email'    => 'Format email tidak valid.',
        'email.max'      => 'Email terlalu panjang.',
        'email.unique'   => 'Email sudah terdaftar.',

        // NO HP
        'no_hp.required' => 'Nomor HP wajib diisi.',
        'no_hp.min'      => 'Nomor HP minimal 10 digit.',
        'no_hp.max'      => 'Nomor HP maksimal 15 digit.',
        'no_hp.regex'    => 'Nomor HP harus format Indonesia (08 atau 628).',
        'no_hp.unique'   => 'Nomor HP sudah terdaftar.',

        // ROLE
        'role.required' => 'Role wajib dipilih.',
        'role.in'       => 'Role tidak valid.',

        // PASSWORD
        'password.required' => 'Kata sandi wajib diisi.',
        'password.min'      => 'Kata sandi minimal 6 karakter.',
        'password.max'      => 'Kata sandi terlalu panjang.',
    ];

    public function togglePassword(): void
    {
        $this->showPassword = ! $this->showPassword;
    }

    public function dismissSuccess()
    {
        $this->showSuccess = false;
    }

    public function save(): void
    {
        $key = 'save-user:' . Auth::id() . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            $seconds = RateLimiter::availableIn($key);

            $this->addError('rate_limit', "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.");
            return;
        }

        RateLimiter::hit($key, 60);
        $validated = $this->validate();

        User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
        ]);


        // Reset form
        $this->reset(['nama', 'nik', 'email', 'no_hp', 'password']);
        $this->role = 'admin';

        $this->showSuccess = true;
        $this->successMessage = 'User berhasil dibuat!';
    }

    public function render()
    {
        return view('livewire.user-create');
    }
}
