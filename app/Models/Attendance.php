<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'menit_terlambat',
        'jam_lembur',
        'keterangan',
    ];

    protected $casts = [
        'tanggal'         => 'date',
        'menit_terlambat' => 'integer',
        'jam_lembur'      => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Label status dalam bahasa Indonesia
     */
    public function getLabelStatusAttribute(): string
    {
        return match($this->status) {
            'hadir'     => 'Hadir',
            'terlambat' => 'Terlambat',
            'izin'      => 'Izin',
            'sakit'     => 'Sakit',
            'alpha'     => 'Alpha',
            default     => '-',
        };
    }

    /**
     * Warna badge per status
     */
    public function getWarnaBadgeAttribute(): string
    {
        return match($this->status) {
            'hadir'     => 'badge-soft-green',
            'terlambat' => 'badge-soft-orange',
            'izin'      => 'badge-soft-blue',
            'sakit'     => 'badge-soft-blue',
            'alpha'     => 'badge-soft-red',
            default     => '',
        };
    }

    /**
     * Hitung menit terlambat otomatis dari jam_masuk.
     * Mengembalikan selisih menit dari jam masuk normal (bukan dari batas toleransi).
     * Toleransi hanya dipakai untuk menentukan status, bukan untuk mengurangi hitungan potongan.
     */
    public static function hitungMenitTerlambat(string $jamMasuk, AttendanceSetting $setting): int
    {
        $masuk       = Carbon::createFromTimeString($jamMasuk);
        $jamNormal   = Carbon::createFromTimeString($setting->jam_masuk_normal);
        $batasToleransi = (clone $jamNormal)->addMinutes($setting->toleransi_menit);

        // Jika masih dalam toleransi, tidak dihitung terlambat
        if ($masuk->lte($batasToleransi)) {
            return 0;
        }

        // Terlambat = selisih dari jam masuk normal
        return $masuk->diffInMinutes($jamNormal);
    }

    /**
     * Hitung jam lembur otomatis dari jam_keluar.
     * Lembur hanya valid jika:
     * 1. Jam keluar melebihi jam pulang normal
     * 2. Karyawan masuk tidak lebih dari 4 jam setelah jam masuk normal
     *    (mencegah karyawan yang masuk sore dihitung lembur)
     */
    public static function hitungJamLembur(string $jamKeluar, AttendanceSetting $setting, ?string $jamMasuk = null): float
    {
        $keluar      = Carbon::createFromTimeString($jamKeluar);
        $batasNormal = Carbon::createFromTimeString($setting->jam_keluar_normal);

        if (!$keluar->gt($batasNormal)) {
            return 0;
        }

        // Validasi: jika ada jam masuk, cek apakah masuk tidak terlalu siang
        if ($jamMasuk) {
            $masuk     = Carbon::createFromTimeString($jamMasuk);
            $jamNormal = Carbon::createFromTimeString($setting->jam_masuk_normal);
            $selisihMasukMenit = $masuk->diffInMinutes($jamNormal, false);

            // Jika masuk lebih dari 4 jam (240 menit) setelah jam normal → tidak dihitung lembur
            if ($selisihMasukMenit > 240) {
                return 0;
            }
        }

        return round($keluar->diffInMinutes($batasNormal) / 60, 2);
    }

    /**
     * Rekap absensi karyawan untuk bulan & tahun tertentu
     */
    public static function rekapBulanan(int $employeeId, int $bulan, int $tahun): array
    {
        $data = self::where('employee_id', $employeeId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $setting = AttendanceSetting::first();
        $hariKerjaStandar = $setting->hari_kerja_sebulan ?? 22;

        $hariHadir    = $data->whereIn('status', ['hadir', 'terlambat'])->count();
        $hariIzin     = $data->where('status', 'izin')->count();
        $hariSakit    = $data->where('status', 'sakit')->count();
        $hariAlpha    = $data->where('status', 'alpha')->count();
        $totalLembur  = $data->sum('jam_lembur');
        $totalTerlambat = $data->sum('menit_terlambat');

        // Hitung potongan
        $potonganTerlambat = $totalTerlambat * ($setting->potongan_per_menit ?? 500);

        // Potongan alpha: jika 0 di setting = potong 1 hari gaji per alpha
        $potonganAlpha = 0;
        if ($setting && $setting->potongan_alpha > 0) {
            $potonganAlpha = $hariAlpha * $setting->potongan_alpha;
        }

        // Potongan izin
        $potonganIzin = 0;
        if ($setting && $setting->potongan_izin > 0) {
            $potonganIzin = $hariIzin * $setting->potongan_izin;
        }

        $totalPotongan = $potonganTerlambat + $potonganAlpha + $potonganIzin;

        return [
            'hari_hadir'         => $hariHadir,
            'hari_izin'          => $hariIzin,
            'hari_sakit'         => $hariSakit,
            'hari_alpha'         => $hariAlpha,
            'total_lembur'       => round($totalLembur, 2),
            'total_terlambat'    => $totalTerlambat,
            'potongan_terlambat' => $potonganTerlambat,
            'potongan_alpha'     => $potonganAlpha,
            'potongan_izin'      => $potonganIzin,
            'total_potongan'     => $totalPotongan,
            'work_days'          => $hariHadir,
            'hari_kerja_standar' => $hariKerjaStandar,
        ];
    }
}
