<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $fillable = [
        'jam_masuk_normal',
        'jam_keluar_normal',
        'toleransi_menit',
        'potongan_per_menit',
        'potongan_alpha',
        'potongan_izin',
        'hari_kerja_sebulan',
    ];

    /**
     * Ambil setting (selalu hanya 1 baris), buat jika belum ada
     */
    public static function getSetting(): self
    {
        return self::firstOrCreate([], [
            'jam_masuk_normal'   => '08:00:00',
            'jam_keluar_normal'  => '17:00:00',
            'toleransi_menit'    => 15,
            'potongan_per_menit' => 500,
            'potongan_alpha'     => 0,
            'potongan_izin'      => 0,
            'hari_kerja_sebulan' => 22,
        ]);
    }
}
