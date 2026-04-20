<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nama'    => 'Super Admin',
            'nik'     => '3175091205980001',
            'email'     => '52023110006@std.umku.ac.id',
            'no_hp'   => '081234567890',
            'role'    => 'super_admin',
            'password'=> 'SuperAdmin@123',
        ]);

    }
}