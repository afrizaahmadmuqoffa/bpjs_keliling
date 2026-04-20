<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            'Pendaftaran Baru',
            'Penambahan Anggota Keluarga',
            'Pengurangan Anggota Keluarga (Pelaporan Peserta Meninggal Dunia dan Rekonsiliasi Data)',
            'Pengurangan Anggota Keluarga (Pelaporan WNI pergi keluar Negeri)',
            'Pengaktifan Kembali Status Kepesertaan (Anak >21 Tahun masih Kuliah)',
            'Pengaktifan Kembali Status Kepesertaan (Data Ganda dan Rekonsiliasi Data)',
            'Pengaktifan Kembali Status Kepesertaan (PBI JK dan PBPU BP Pemda)',
            'Pengaktifan Kembali Status Kepesertaan (Registrasi Ulang dan Rekonsiliasi Data)',
            'Pengaktifan Kembali Status Kepesertaan (Update VA PBPU)',
            'Pengaktifan Kembali Status Kepesertaan (WNI Kembali dari Luar Negeri)',
            'Peralihan Jenis Kepesertaan',
            'Peralihan Jenis Kepesertaan (Tanpa Administrasi 14 Hari)',
            'Perubahan/Perbaikan Data FKTP',
            'Perubahan/Perbaikan Data Golongan dan Gaji',
            'Perubahan/Perbaikan Data Identitas (NIK, No KK, Nama, Tanggal Lahir, Jenis Kelamin, Alamat)',
            'Perubahan/Perbaikan Data Kelas Rawat',
            'Perubahan/Perbaikan Data Nomor Handphone',
            'Perubahan/Perbaikan Data Pembaharuan KK (Gabung/Pisah KK)',
            'Rekonsiliasi Iuran (Refund Iuran)',
            'Rekonsiliasi Iuran (VA to VA)',
            'Pemberian Informasi',
            'Penanganan Pengaduan',
        ];

        foreach ($services as $service) {
            DB::table('services')->updateOrInsert(
                ['nama' => $service], // unique key
                [
                    'id' => Str::uuid7(), // UUID generate
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
