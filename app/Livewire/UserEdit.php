<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\RateLimiter;

class UserEdit extends Component
{
    public string    $userId;
    public string $nama     = '';
    public string $nik      = '';
    public string $email    = '';
    public string $no_hp    = '';
    public string $role     = 'admin';
    public string $password = '';

    public bool $showPassword = false;
    public bool $showSuccess = false;
    public string $successMessage = '';

    public function mount(string $id): void
    {
        $user = User::findOrFail($id);

        // Hanya super_admin yang bisa edit akun super_admin lain
        if ($user->role === 'super_admin' && Auth::user()->role !== 'super_admin') {
            abort(403, 'Tidak bisa mengubah akun super admin.');
        }

        $this->userId = $user->id;
        $this->nama   = $user->nama;
        $this->nik    = $user->nik;
        $this->email  = $user->email;
        $this->no_hp  = $user->no_hp ?? '';
        $this->role   = $user->role;
    }

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
                    ->ignore($this->userId)
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($this->userId)
            ],

            'no_hp' => [
                'nullable',
                'string',
                'min:10',
                'max:15',
                'regex:/^(08|628)[0-9]+$/',
                Rule::unique('users', 'no_hp')
                    ->ignore($this->userId)
            ],

            'role' => [
                'required',
                Rule::in($isSuperAdmin ? ['super_admin', 'admin', 'pic'] : ['admin', 'pic'])
            ],

            'password' => [
                'nullable',
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
        'nik.unique'   => 'NIK sudah digunakan akun lain.',

        // EMAIL
        'email.required' => 'Email wajib diisi.',
        'email.email'    => 'Format email tidak valid.',
        'email.max'      => 'Email terlalu panjang.',
        'email.unique'   => 'Email sudah digunakan akun lain.',

        // NO HP
        'no_hp.min'    => 'Nomor HP minimal 10 digit.',
        'no_hp.max'    => 'Nomor HP maksimal 15 digit.',
        'no_hp.regex'  => 'Nomor HP harus format Indonesia (08 atau 628).',
        'no_hp.unique' => 'Nomor HP sudah digunakan akun lain.',

        // ROLE
        'role.required' => 'Role wajib dipilih.',
        'role.in'       => 'Role tidak valid.',

        // PASSWORD
        'password.min' => 'Kata sandi minimal 6 karakter.',
        'password.max' => 'Kata sandi terlalu panjang.',
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
        $key = 'edit-participant:' . Auth::id() . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            $seconds = RateLimiter::availableIn($key);

            $this->addError('rate_limit', "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.");
            return;
        }

        RateLimiter::hit($key, 60);
        $validated = $this->validate();

        $data = collect($validated)
            ->except('password')
            ->toArray();

        // Hanya update password jika diisi
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        User::findOrFail($this->userId)->update($data);

        $this->password = '';
        $this->showSuccess  = true;
        $this->successMessage = 'Data user berhasil diperbarui!';
    }

    public function render()
    {
        return view('livewire.user-edit');
    }
}
