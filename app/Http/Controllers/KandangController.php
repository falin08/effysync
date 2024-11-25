<?php

namespace App\Http\Controllers;
use App\Models\Kandang;
use App\Models\LaporanHarian;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class KandangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role === 'admin') {
            // Admin bisa melihat semua kandang
            $kandangs = Kandang::orderBy('created_at', 'DESC')->get();
        } else {
            // User biasa hanya bisa melihat kandang aktif
            $kandangs = Kandang::whereNull('deactivated_at')
                ->orderBy('created_at', 'DESC')
                ->get();
        }

        return response()->json([
            'status' => Response::HTTP_OK,
            'data' => $kandangs,
        ]);
    }
    
    // Menambahkan kandang baru
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|max:255|unique:kandangs,kode',
            'jumlah_unggas' => 'required|integer|min:1',
            'jenis_unggas' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'errors' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

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
        try{
            $kandang = Kandang::findOrFail($id);
            
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $kandang,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Kandang tidak ditemukan.',
            ], Response::HTTP_NOT_FOUND);
        }
     }

    // Mengupdate data kandang
    public function update(Request $request, $id)
    {
        $kandang = Kandang::findOrFail($id);

        $request->validate([
            'kode' => 'sometimes|string|max:255|unique:kandangs,kode,' . $id,
            'jumlah_unggas' => 'sometimes|integer|min:0',
            'jenis_unggas' => 'sometimes|string',
            'status' => 'sometimes|in:aktif,tidak aktif',
        ]);

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
        if ($request->has('status')) {
            if ($request['status'] === 'tidak aktif') {
                $kandang->status = 'tidak aktif';
                // Konversi waktu UTC ke Asia/Jakarta
                $kandang->deactivated_at = now()->timezone('Asia/Jakarta');
            } else {
                $kandang->status = 'aktif';
                $kandang->deactivated_at = null;
            }
        }

        $kandang->save();

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Kandang berhasil diperbarui.',
            'data' => $kandang,
        ]);
    }

    public function getUnggasInfo($id_kandang)
    {
        $kandang = Kandang::findOrFail($id_kandang);

        $populasiAwal = $kandang->jumlah_unggas;

        //Mengambil data dari fungsi getDeplesi
        $deplesiResponse = $this->getDeplesi($id_kandang);

        // Mengambil data JSON dari response
        $deplesiData = json_decode($deplesiResponse->getContent(), true);

        
        $deplesiUnggas = $deplesiData['data']['deplesi'];
        $populasiSekarang = max(0, $populasiAwal - $deplesiUnggas);

        // Menghitung persentase kematian dan tingkat kesehatan
        $persentaseKematian = ($populasiAwal > 0) ? ($deplesiData['data']['jumlah_mati'] / $populasiAwal) * 100 : 0;
        $tingkatKesehatan = ($populasiAwal > 0) ? ($populasiSekarang / $populasiAwal) * 100 : 0;

        return response()->json([
            'status' => Response::HTTP_OK,
            'data' => [
                'populasi_awal' => $populasiAwal,
                'populasi_sekarang' => $populasiSekarang,
                'persentase_kematian' => $persentaseKematian,
                'tingkat_kesehatan' => $tingkatKesehatan,
            ]
        ]);
    }

    public function getDeplesi($id_kandang)
    {
        $laporan = LaporanHarian::where('id_kandang', $id_kandang)
            ->selectRaw('
                SUM(kematian) as total_kematian, 
                SUM(jumlah_sakit) as total_sakit
            ')
            ->first();

        $totalKematian = $laporan->total_kematian ?? 0;
        $totalSakit = $laporan->total_sakit ?? 0;
        $deplesiUnggas = $totalKematian + $totalSakit;

        return response()->json([
            'status' => Response::HTTP_OK,
            'data' => [
            'jumlah_mati' => $totalKematian,
            'jumlah_sakit' => $totalSakit,
            'deplesi' => $deplesiUnggas,
        ]
    ]);
    }

    public function getHistoryPenyakit($id_kandang)
    {
        // Ambil data laporan dengan jumlah sakit > 0
        $laporanSakit = LaporanHarian::with('penyakit')
            ->where('id_kandang', $id_kandang)
            ->where('jumlah_sakit', '>', 0)
            ->get();

        // Jika tidak ada laporan
        if ($laporanSakit->isEmpty()) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Tidak ada riwayat penyakit untuk kandang ini.',
            ]);
        }

        $history = $laporanSakit->map(function ($laporan) {
            return [
                'tanggal_laporan' => $laporan->created_at,
                'jumlah_unggas' => $laporan->jumlah_sakit,
                'gejala' => $laporan->penyakit->gejala ?? 'Tidak ada gejala',
                'nama_penyakit' => $laporan->penyakit->nama ?? 'Tidak ada nama penyakit',
                'deskripsi_penyakit' => $laporan->penyakit->deskripsi ?? 'Tidak ada deskripsi',
                'pengobatan' => $laporan->penyakit->pengobatan ?? 'Tidak ada pengobatan',
            ];
        });

        return response()->json([
            'status' => Response::HTTP_OK,
            'data' => [
                'total_sakit' => $laporanSakit->sum('jumlah_sakit'),
                'history' => $history,
            ]
        ]);
    }

    public function getLaporanHarian($id_kandang, Request $request)
    {
        // Ambil tanggal dari request atau gunakan tanggal hari ini
        $tanggal = $request->input('tanggal', now()->toDateString());

        $laporanHarian = LaporanHarian::with('pakan:id,nama,jenis')
            ->where('id_kandang', $id_kandang)
            ->whereDate('created_at', $tanggal)
            ->orderBy('created_at', 'DESC')
            ->get(['created_at', 'telur as jumlah_telur', 'id_pakan', 'jumlah_pakan']);

        if ($laporanHarian->isEmpty()) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Tidak ada laporan harian untuk tanggal ini.',
            ]);
        }

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Laporan harian berhasil diambil.',
            'data' => $laporanHarian,
        ]);
    }
}
