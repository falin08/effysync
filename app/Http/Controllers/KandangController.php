<?php

namespace App\Http\Controllers;
use App\Models\Kandang;
use App\Models\User;
use App\Models\LaporanHarian;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Cast\String_;

class KandangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role === 'admin') {
            // Admin bisa melihat semua kandang
            $kandangs = Kandang::all();
        } else {
            // User biasa hanya bisa melihat kandang aktif
            $kandangs = Kandang::whereNull('deactivated_at')->get();
        }

        return response()->json([
            'status' => Response::HTTP_OK,
            'data' => $kandangs,
        ]);
    }
    
    // Menambahkan kandang baru
    public function create(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:255|unique:kandangs,kode',
            'jumlah_unggas' => 'required|integer|min:1',
            'jenis_unggas' => 'required|string',
        ]);

        // Set deactivated_at menjadi null saat membuat kandang baru
        $kandang = Kandang::create(array_merge($request->all(), [
            'deactivated_at' => null,
            'status' => 'aktif'
        ]));

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Kandang berhasil ditambahkan.',
            'data' => $kandang,
        ], Response::HTTP_CREATED);
    }

     // Menampilkan detail kandang berdasarkan ID
     public function show($id)
     {
         $kandang = Kandang::findOrFail($id);
         
         return response()->json([
             'status' => Response::HTTP_OK,
             'message' => 'Success',
             'data' => $kandang,
         ]);
     }

    // Mengupdate data kandang
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'sometimes|string|max:255|unique:kandangs,kode,'.$id,
            'jumlah_unggas' => 'sometimes|integer|min:1',
            'jenis_unggas' => 'sometimes|string',
            'status' => 'sometimes|in:aktif,tidak aktif',
        ]);

        $kandang = Kandang::findOrFail($id);

         // Pastikan 'kode' tidak kosong atau null
        if ($request->has('kode') && empty($request->kode)) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Kode tidak boleh kosong',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Update data kandang
        if ($request->has('kode')) {
            $kandang->kode = $request->kode;
        }
        if ($request->has('jumlah_unggas')) {
            $kandang->jumlah_unggas = $request->jumlah_unggas;
        }
        if ($request->has('jenis_unggas')) {
            $kandang->jenis_unggas = $request->jenis_unggas;
        }

        // Update deactivated_at jika status menjadi tidak aktif
        if ($request->status === 'tidak aktif') {
            $kandang->deactivated_at = now();
        } else {
            $kandang->deactivated_at = null;
        }
        $kandang->status = $request->status ?? 'aktif'; // Mengupdate status sesuai request

        $kandang->save();

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Kandang berhasil diperbarui.',
            'data' => $kandang,
        ]);
    }

    // Menghapus kandang
    public function delete($id)
    {
        $kandang = Kandang::findOrFail($id);
        $kandang->delete(); // Ini akan memanggil soft delete

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Kandang berhasil dihapus (soft delete).',
        ]);
    }

    public function getUnggasInfo($id_kandang)
    {
        // Ambil data kandang berdasarkan ID
        $kandang = Kandang::find($id_kandang);
        if (!$kandang) {
            return response()->json([
                'status' => 404,
                'message' => 'Kandang tidak ditemukan'
            ], 404);
        }

        // Ambil populasi awal dari kolom `jumlah_unggas` pada data kandang
        $populasiAwal = $kandang->jumlah_unggas;

        // Hitung total kematian dan sakit dari tabel laporan_harians
        $laporan = LaporanHarian::where('id_kandang', $id_kandang);

        $totalKematian = $laporan->sum('kematian');
        $totalSakit = $laporan->sum('jumlah_sakit');

        // Hitung populasi sekarang: populasi awal - total kematian dan sakit
        $populasiSekarang = max(0, $populasiAwal - ($totalKematian + $totalSakit));

        // Hitung deplesi unggas (jumlah sakit + mati)
        $deplesiUnggas = $totalKematian + $totalSakit;

        // Menghitung persentase kematian dan tingkat kesehatan
        $persentaseKematian = ($populasiAwal > 0) ? ($totalKematian / $populasiAwal) * 100 : 0;
        $tingkatKesehatan = ($populasiAwal > 0) ? ($populasiSekarang / $populasiAwal) * 100 : 0;
        
        return response()->json([
            'status' => 200,
            'data' => [
                'populasi_awal' => $populasiAwal,
                'populasi_sekarang' => $populasiSekarang,
                'jumlah_sakit' => $totalSakit,
                'jumlah_mati' => $totalKematian,
                'deplesi_unggas' => $deplesiUnggas, 
                'persentase_kematian' => $persentaseKematian,
                'tingkat_kesehatan' => $tingkatKesehatan,
            ]
        ], 200);
    }

    public function getHistoryPenyakit($id_kandang)
    {
        // Ambil data laporan dengan jumlah sakit > 0
        $laporanSakit = LaporanHarian::with(['penyakit'])
            ->where('id_kandang', $id_kandang)
            ->where('jumlah_sakit', '>', 0)
            ->get();

        // Jika tidak ada laporan
        if ($laporanSakit->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada riwayat penyakit untuk kandang ini.'
            ], 404);
        }

        // Menghitung total jumlah unggas sakit
        $totalSakit = $laporanSakit->sum('jumlah_sakit');

        return response()->json([
            'status' => 200,
            'data' => [
                'total_sakit' => $totalSakit,
                'history' => $laporanSakit->map(function ($laporan) {
                    return [
                        'tanggal_laporan' => $laporan->created_at->format('Y-m-d'),
                        'jumlah_unggas' => $laporan->jumlah_sakit ?? 'Tidak ada unggas sakit',
                        'gejala' => $laporan->penyakit->gejala ?? 'Tidak ada gejala',
                        'nama_penyakit' => $laporan->penyakit->nama ?? 'Tidak ada nama penyakit',
                        'deskripsi_penyakit' => $laporan->penyakit->deskripsi ?? 'Tidak ada deskripsi',
                        'pengobatan' => $laporan->penyakit->pengobatan ?? 'Tidak ada pengobatan',
                    ];
                })
            ]
        ], 200);
    }

    public function getLaporanHarian($id_kandang, Request $request)
    {
        // Ambil tanggal dari request atau gunakan tanggal hari ini
        $tanggal = $request->tanggal ?? now()->toDateString();

        // Query laporan harian berdasarkan ID kandang dan tanggal
        $laporanHarian = LaporanHarian::with(['pakan', 'penyakit', 'user'])
            ->where('id_kandang', $id_kandang)
            ->whereDate('created_at', $tanggal) // Filter berdasarkan tanggal
            ->get();

        // Jika tidak ada laporan
        if ($laporanHarian->isEmpty()) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Tidak ada laporan harian untuk tanggal ini.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Laporan harian berhasil diambil.',
            'data' => $laporanHarian,
        ], Response::HTTP_OK);
    }
}
