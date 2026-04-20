<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SegmentModel extends Model
{
    use HasUuids;

    protected $table = 'segments';

    protected $fillable = ['nama'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function participants()
    {
        return $this->hasMany(ParticipantModel::class, 'segment_id');
    }

}
