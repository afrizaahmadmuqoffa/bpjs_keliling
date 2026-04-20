<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ServiceModel extends Model
{
    use HasUuids;

    protected $table = 'services';

    protected $fillable = [
        'nama',
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

    // 1 layanan dipakai banyak peserta
    public function participants()
    {
        return $this->hasMany(ParticipantModel::class, 'layanan_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */

    // search layanan
    public function scopeSearch($query, $keyword)
    {
        return $query->where('nama', 'like', "%{$keyword}%");
    }
}