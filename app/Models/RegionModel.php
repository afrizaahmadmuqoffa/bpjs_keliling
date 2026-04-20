<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RegionModel extends Model
{
    use HasUuids;

    protected $table = 'regions';

    protected $fillable = [
        'kecamatan',
        'kelurahan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    // 1 region punya banyak peserta
    public function participants()
    {
        return $this->hasMany(ParticipantModel::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR (UX)
    |--------------------------------------------------------------------------
    */

    // buat tampil: "Kecamatan - Kelurahan"
    public function getFullNameAttribute()
    {
        return "{$this->kecamatan} - {$this->kelurahan}";
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE (QUERY CLEAN)
    |--------------------------------------------------------------------------
    */

    // filter by kecamatan
    public function scopeByKecamatan($query, $kecamatan)
    {
        return $query->where('kecamatan', $kecamatan);
    }
}