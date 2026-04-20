<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/region.xlsx');

        (new FastExcel)->import($path, function ($row) {

            // 🔥 NORMALISASI
            $row = array_change_key_case($row, CASE_LOWER);
            $provinsi = strtolower(trim($row['nama_prop'] ?? ''));
            $kabupaten = strtolower(trim(str_replace('KABUPATEN ', '', $row['nama_kab'] ?? '')));
            $kecamatan = strtolower(trim($row['nama_kec'] ?? ''));
            $kelurahan = strtolower(trim($row['nama_kel'] ?? ''));

            if (!$kabupaten || !$kecamatan || !$kelurahan) {
                return;
            }

            DB::table('regions')->updateOrInsert(
                [
                    'provinsi'  => $provinsi,
                    'kabupaten' => $kabupaten,
                    'kecamatan' => $kecamatan,
                    'kelurahan' => $kelurahan,
                ],
                [
                    'id' => Str::uuid7(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        });
    }
}
