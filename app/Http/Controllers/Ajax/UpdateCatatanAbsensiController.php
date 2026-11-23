<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\CatatanAbsensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateCatatanAbsensiController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::guard('karyawan')->user();

        if (! $user || (! $user->hasPermission('admin_log_presensi') && ! $user->hasPermission('admin_data_karyawan'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak. Anda tidak memiliki otoritas mengubah catatan absensi.',
            ], 403);
        }

        $data = $request->validate([
            'karyawan_id' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $emp = Karyawan::find($data['karyawan_id']);

        if (! $emp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Karyawan dengan ID tersebut tidak ditemukan di database.',
            ]);
        }

        $catatan = trim((string) ($data['catatan'] ?? ''));

        if ($catatan === '') {
            CatatanAbsensi::where('karyawan_id', $emp->id)->whereDate('tanggal', $data['tanggal'])->delete();

            return response()->json([
                'status' => 'success',
                'message' => "Catatan absensi {$emp->nama} pada tanggal {$data['tanggal']} berhasil dihapus.",
            ]);
        }

        $row = CatatanAbsensi::updateOrCreate(
            ['karyawan_id' => $emp->id, 'tanggal' => $data['tanggal']],
            [
                'catatan' => $catatan,
                'admin_id' => $user->id,
                'admin_name' => $user->nama,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => "Catatan absensi {$emp->nama} pada tanggal {$data['tanggal']} berhasil disimpan.",
            'data' => ['id' => $row->id],
        ]);
    }
}
