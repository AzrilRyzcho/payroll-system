<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $bulan    = $request->get('bulan', now()->month);
        $tahun    = $request->get('tahun', now()->year);
        $cariId   = $request->get('karyawan_id');

        $query = Attendance::with('employee.role')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc');

        if ($cariId) {
            $query->where('employee_id', $cariId);
        }

        $absensi  = $query->paginate(20)->appends($request->query());
        $karyawan = Employee::orderBy('name')->get();
        $setting  = AttendanceSetting::getSetting();

        return view('absensi.index', compact('absensi', 'karyawan', 'bulan', 'tahun', 'setting'));
    }

    public function create()
    {
        $karyawan = Employee::orderBy('name')->get();
        $setting  = AttendanceSetting::getSetting();
        return view('absensi.create', compact('karyawan', 'setting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal'     => 'required|date',
            'jam_masuk'   => 'nullable|date_format:H:i',
            'jam_keluar'  => 'nullable|date_format:H:i|after:jam_masuk',
            'status'      => 'required|in:hadir,terlambat,izin,sakit,alpha',
            'keterangan'  => 'nullable|string|max:500',
        ]);

        $setting        = AttendanceSetting::getSetting();
        $menitTerlambat = 0;
        $jamLembur      = 0;
        $status         = $request->status;

        if ($request->jam_masuk) {
            $menitTerlambat = Attendance::hitungMenitTerlambat($request->jam_masuk . ':00', $setting);
            // Auto set status terlambat jika ada keterlambatan
            if ($menitTerlambat > 0 && $status === 'hadir') {
                $status = 'terlambat';
            }
        }

        if ($request->jam_keluar) {
            $jamLembur = Attendance::hitungJamLembur(
                $request->jam_keluar . ':00',
                $setting,
                $request->jam_masuk ? $request->jam_masuk . ':00' : null
            );
        }

        $data = [
            'employee_id'     => $request->employee_id,
            'tanggal'         => $request->tanggal,
            'jam_masuk'       => $request->jam_masuk ? $request->jam_masuk . ':00' : null,
            'jam_keluar'      => $request->jam_keluar ? $request->jam_keluar . ':00' : null,
            'status'          => $status,
            'menit_terlambat' => $menitTerlambat,
            'jam_lembur'      => $jamLembur,
            'keterangan'      => $request->keterangan,
        ];

        if ($request->ajax()) {
            // Cek duplikat
            $exists = Attendance::where('employee_id', $request->employee_id)
                ->where('tanggal', $request->tanggal)->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data absensi karyawan ini pada tanggal tersebut sudah ada.',
                ], 422);
            }

            $item = Attendance::create($data);
            $item->load('employee');

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil dicatat.',
                'data'    => [
                    'id'              => $item->id,
                    'nama'            => $item->employee->name,
                    'tanggal'         => Carbon::parse($item->tanggal)->format('d M Y'),
                    'jam_masuk'       => $item->jam_masuk ? substr($item->jam_masuk, 0, 5) : '-',
                    'jam_keluar'      => $item->jam_keluar ? substr($item->jam_keluar, 0, 5) : '-',
                    'status'          => $item->label_status,
                    'warna'           => $item->warna_badge,
                    'menit_terlambat' => $item->menit_terlambat,
                    'jam_lembur'      => $item->jam_lembur,
                ],
            ]);
        }

        Attendance::create($data);

        return redirect()->route('absensi.index')
            ->with('success', 'Absensi berhasil dicatat.');
    }

    public function edit(Attendance $absensi)
    {
        if (request()->ajax()) {
            return response()->json([
                'id'          => $absensi->id,
                'employee_id' => $absensi->employee_id,
                'tanggal'     => $absensi->tanggal->format('Y-m-d'),
                'jam_masuk'   => $absensi->jam_masuk ? substr($absensi->jam_masuk, 0, 5) : '',
                'jam_keluar'  => $absensi->jam_keluar ? substr($absensi->jam_keluar, 0, 5) : '',
                'status'      => $absensi->status,
                'keterangan'  => $absensi->keterangan,
            ]);
        }

        $karyawan = Employee::orderBy('name')->get();
        $setting  = AttendanceSetting::getSetting();
        return view('absensi.edit', compact('absensi', 'karyawan', 'setting'));
    }

    public function update(Request $request, Attendance $absensi)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal'     => 'required|date',
            'jam_masuk'   => 'nullable|date_format:H:i',
            'jam_keluar'  => 'nullable|date_format:H:i',
            'status'      => 'required|in:hadir,terlambat,izin,sakit,alpha',
            'keterangan'  => 'nullable|string|max:500',
        ]);

        $setting        = AttendanceSetting::getSetting();
        $menitTerlambat = 0;
        $jamLembur      = 0;
        $status         = $request->status;

        if ($request->jam_masuk) {
            $menitTerlambat = Attendance::hitungMenitTerlambat($request->jam_masuk . ':00', $setting);
            if ($menitTerlambat > 0 && $status === 'hadir') {
                $status = 'terlambat';
            }
        }

        if ($request->jam_keluar) {
            $jamLembur = Attendance::hitungJamLembur(
                $request->jam_keluar . ':00',
                $setting,
                $request->jam_masuk ? $request->jam_masuk . ':00' : null
            );
        }

        $absensi->update([
            'employee_id'     => $request->employee_id,
            'tanggal'         => $request->tanggal,
            'jam_masuk'       => $request->jam_masuk ? $request->jam_masuk . ':00' : null,
            'jam_keluar'      => $request->jam_keluar ? $request->jam_keluar . ':00' : null,
            'status'          => $status,
            'menit_terlambat' => $menitTerlambat,
            'jam_lembur'      => $jamLembur,
            'keterangan'      => $request->keterangan,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil diperbarui.',
            ]);
        }

        return redirect()->route('absensi.index')
            ->with('success', 'Absensi berhasil diperbarui.');
    }

    public function destroy(Attendance $absensi)
    {
        $absensi->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Absensi berhasil dihapus.']);
        }

        return redirect()->route('absensi.index')->with('success', 'Absensi berhasil dihapus.');
    }

    /**
     * Rekap absensi per karyawan per bulan (AJAX)
     */
    public function rekap(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'bulan'       => 'required|integer|min:1|max:12',
            'tahun'       => 'required|integer|min:2020',
        ]);

        $rekap = Attendance::rekapBulanan(
            $request->employee_id,
            $request->bulan,
            $request->tahun
        );

        $employee = Employee::with('role')->find($request->employee_id);
        $gajiPokok = $employee->role->salary ?? 0;

        // Hitung potongan alpha = 1 hari gaji jika setting = 0
        $setting = AttendanceSetting::getSetting();
        if ($setting->potongan_alpha == 0 && $rekap['hari_alpha'] > 0) {
            $gajiHarian = $gajiPokok / $setting->hari_kerja_sebulan;
            $rekap['potongan_alpha'] = $gajiHarian * $rekap['hari_alpha'];
            $rekap['total_potongan'] = $rekap['potongan_terlambat']
                + $rekap['potongan_alpha']
                + $rekap['potongan_izin'];
        }

        return response()->json([
            'rekap'    => $rekap,
            'formatted' => [
                'potongan_terlambat' => 'Rp ' . number_format($rekap['potongan_terlambat'], 0, ',', '.'),
                'potongan_alpha'     => 'Rp ' . number_format($rekap['potongan_alpha'], 0, ',', '.'),
                'potongan_izin'      => 'Rp ' . number_format($rekap['potongan_izin'], 0, ',', '.'),
                'total_potongan'     => 'Rp ' . number_format($rekap['total_potongan'], 0, ',', '.'),
            ],
        ]);
    }

    /**
     * Halaman pengaturan absensi
     */
    public function pengaturan()
    {
        $setting = AttendanceSetting::getSetting();
        return view('absensi.pengaturan', compact('setting'));
    }

    public function simpanPengaturan(Request $request)
    {
        $request->validate([
            'jam_masuk_normal'   => 'required|date_format:H:i',
            'jam_keluar_normal'  => 'required|date_format:H:i',
            'toleransi_menit'    => 'required|integer|min:0|max:60',
            'potongan_per_menit' => 'required|numeric|min:0',
            'potongan_alpha'     => 'required|numeric|min:0',
            'potongan_izin'      => 'required|numeric|min:0',
            'hari_kerja_sebulan' => 'required|integer|min:1|max:31',
        ]);

        $setting = AttendanceSetting::getSetting();
        $setting->update([
            'jam_masuk_normal'   => $request->jam_masuk_normal . ':00',
            'jam_keluar_normal'  => $request->jam_keluar_normal . ':00',
            'toleransi_menit'    => $request->toleransi_menit,
            'potongan_per_menit' => $request->potongan_per_menit,
            'potongan_alpha'     => $request->potongan_alpha,
            'potongan_izin'      => $request->potongan_izin,
            'hari_kerja_sebulan' => $request->hari_kerja_sebulan,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pengaturan berhasil disimpan.']);
        }

        return redirect()->route('absensi.pengaturan')
            ->with('success', 'Pengaturan absensi berhasil disimpan.');
    }
}
