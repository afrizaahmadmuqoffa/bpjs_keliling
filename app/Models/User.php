<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasUuids, Notifiable;

    protected $fillable = [
        'nama',
        'nik',
        'email',
        'no_hp',
        'role',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getNikMaskedAttribute()
    {
        return substr($this->nik, 0, 4) . '****' . substr($this->nik, -4);
    }

    // 🔥 AUTO FORMAT NO HP KE 62 SAAT DISIMPAN
    public function setNoHpAttribute($value)
    {
        if (!$value) {
            $this->attributes['no_hp'] = null;
            return;
        }

        // hapus semua karakter non angka
        $value = preg_replace('/\D/', '', $value);

        // ubah ke format 62
        if (str_starts_with($value, '0')) {
            $value = '62' . substr($value, 1);
        } elseif (str_starts_with($value, '62')) {
            // sudah benar → biarkan
        } else {
            // fallback kalau aneh (misal: 812xxx)
            $value = '62' . $value;
        }

        $this->attributes['no_hp'] = $value;
    }
}
