<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('nik', 16)->unique();
            $table->string('no_hp', 20)->unique();
            $table->text('alamat');

            $table->foreignUuid('region_id')
                ->constrained('regions')
                ->cascadeOnDelete();

            $table->foreignUuid('layanan_id')
                ->nullable()
                ->constrained('services')
                ->nullOnDelete();

            $table->enum('status', ['pending', 'selesai'])
                ->default('pending');

            $table->foreignUuid('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignUuid('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('tanggal_selesai')->nullable();
            $table->softDeletes();
            $table->index('status');
            $table->index('region_id');
            $table->index('created_by');
            $table->index('processed_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
