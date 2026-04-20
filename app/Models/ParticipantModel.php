<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParticipantModel extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'participants';

    protected $fillable = [
        'nama',
        'nik',
        'no_hp',
        'alamat',
        'region_id',
        'status',
        'created_by',
        'processed_by',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_selesai' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    // peserta -> region
    public function region()
    {
        return $this->belongsTo(RegionModel::class);
    }

    // peserta -> layanan
    public function service()
    {
        return $this->belongsTo(ServiceModel::class, 'layanan_id');
    }

    public function segment()
    {
        return $this->belongsTo(SegmentModel::class, 'segment_id');
    }

    // peserta dibuat oleh user
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // peserta diproses oleh user
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR (UX & DISPLAY)
    |--------------------------------------------------------------------------
    */

    // masking NIK (aman ditampilkan)
    public function getNikMaskedAttribute()
    {
        return substr($this->nik, 0, 4) . '****' . substr($this->nik, -4);
    }

    // label status (buat badge UI)
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'selesai' => 'Selesai',
            default => 'Unknown',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE (QUERY CLEAN)
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeByRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama', 'like', "%{$keyword}%")
                ->orWhere('nik', 'like', "%{$keyword}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATOR (DATA CLEANING)
    |--------------------------------------------------------------------------
    */

    // auto trim nama
    public function setNamaAttribute($value)
    {
        $this->attributes['nama'] = trim($value);
    }

    // pastikan NIK selalu angka
    public function setNikAttribute($value)
    {
        $this->attributes['nik'] = preg_replace('/\D/', '', $value);
    }

    // pastikan nomor HP selalu format WA (62...)
    public function setNoHpAttribute($value)
    {
        // hapus semua karakter selain angka
        $no = preg_replace('/\D/', '', $value);

        // jika diawali 0 → ubah ke 62
        if (substr($no, 0, 1) === '0') {
            $no = '62' . substr($no, 1);
        }

        // jika diawali 8 (user kadang tulis tanpa 0)
        if (substr($no, 0, 1) === '8') {
            $no = '62' . $no;
        }

        // jika diawali 62 → biarkan

        $this->attributes['no_hp'] = $no;
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC (IMPORTANT)
    |--------------------------------------------------------------------------
    */

    // tandai selesai
    public function markAsSelesai($userId)
    {
        $this->update([
            'status' => 'selesai',
            'processed_by' => $userId,
            'tanggal_selesai' => now(),
        ]);
    }
}
