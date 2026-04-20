<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
        public function run(): void
    {
        $segments = [
            'PBI JAMINAN KESEHATAN',
            'PBPU DAN BP PEMERINTAH DAERAH',
            'ANGGOTA POLRI',
            'DEWAN PERWAKILAN RAKYAT DAERAH',
            'KEPALA DESA DAN PERANGKAT DESA',
            'PEGAWAI PEMERINTAH DENGAN PERJANJIAN KERJA',
            'PEJABAT NEGARA',
            'PNS MABES DAN KEMHAN',
            'PNS DAERAH',
            'PNS DAERAH DIPERBANTUKAN',
            'PNS PUSAT',
            'PNS PUSAT DIPERBANTUKAN',
            'PRAJURIT AD',
            'PRAJURIT AU',
            'PRAJURIT AL',
            'PEGAWAI BUMN',
            'PEGAWAI SWASTA',
            'PEGAWAI BUMD',
            'PEKERJA MANDIRI',
            'VETERAN',
            'INVESTOR',
            'PEMBERI KERJA',
            'PENERIMA PENSIUN PNS',
            'PENERIMA PENSIUN POLRI',
            'PENERIMA PENSIUN SWASTA',
            'PENERIMA PENSIUN TNI',
            'PENERIMA PENSIUN PEJABAT NEGARA',
            'PERINTIS KEMERDRKAAN',
        ];

        foreach ($segments as $s) {
            DB::table('segments')->updateOrInsert(
                ['nama' => $s],
                [
                    'id' => Str::uuid7(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
