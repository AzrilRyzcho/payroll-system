<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->time('jam_masuk_normal')->default('08:00:00');   // batas masuk normal
            $table->time('jam_keluar_normal')->default('17:00:00');  // jam pulang normal
            $table->integer('toleransi_menit')->default(15);         // toleransi terlambat (menit)
            $table->decimal('potongan_per_menit', 10, 2)->default(500);  // potongan per menit terlambat
            $table->decimal('potongan_alpha', 10, 2)->default(0);    // potongan alpha (0 = 1 hari gaji)
            $table->decimal('potongan_izin', 10, 2)->default(0);     // potongan izin (0 = tidak dipotong)
            $table->integer('hari_kerja_sebulan')->default(22);      // standar hari kerja
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
    }
};
