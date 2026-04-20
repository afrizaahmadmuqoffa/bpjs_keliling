<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus unique index dari no_hp dan nik di tabel participants.
     * Uniqueness tetap dijaga di level Laravel (withoutTrashed),
     * sehingga soft-deleted records tidak dianggap duplikat.
     */
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropUnique('participants_no_hp_unique');
            $table->dropUnique('participants_nik_unique');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->unique('no_hp');
            $table->unique('nik');
        });
    }
};
