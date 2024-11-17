<?php

namespace App\Http\Controllers;

use App\Models\Kandang;
use App\Models\LaporanHarian;
use App\Models\Penyakit;
use App\Models\Pakan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LaporanHarianController extends Controller
{
    public function store(Request $request, $id_kandang)
    {
        $request->validate([
            'id_pakan' => 'required|exists:pakans,id',
            'jumlah_pakan' => 'required|numeric|min:0',
            'telur' => 'nullable|integer|min:0',
            'kematian' => 'nullable|integer|min:0',
            'jumlah_sakit' => 'nullable|integer|min:0',
            'id_penyakit' => 'nullable|exists:penyakits,id',
        ],
        [
            'id_penyakit.required_if' => 'Gejala penyakit harus dipilih jika ada unggas sakit.',
        ]);
        
        // Validasi tambahan jika jumlah sakit diisi, id_penyakit harus diisi
        if ($request->jumlah_sakit > 0 && !$request->id_penyakit) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Jika jumlah sakit diisi, gejala penyakit harus dipilih.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Ambil informasi dan ketersediaan pakan
        $pakan = Pakan::findOrFail($request->id_pakan);

        //ambil jenis dan nama pakan
        $jenis_pakan = $pakan->jenis;
        $nama_pakan = $pakan->nama;

        //kurangi stok di database
        if ($pakan->stok < $request->jumlah_pakan) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Stok pakan tidak mencukupi.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Ambil informasi penyakit dan pengobatan
        $penyakit = null;
        $pengobatan = null;
        $nama_penyakit = null;
        $deskripsi_penyakit = null;
        if ($request->id_penyakit) {
            $penyakit = Penyakit::find($request->id_penyakit);
            $pengobatan = $penyakit ? $penyakit->pengobatan : null;
            $nama_penyakit = $penyakit ? $penyakit->nama: null;
            $deskripsi_penyakit = $penyakit ? $penyakit->deskripsi: null;
        }

        // Kurangi stok pakan
        $pakan->stok -= $request->jumlah_pakan;
        $pakan->save();

        // Simpan laporan harian
        $laporan = LaporanHarian::create([
            'id_kandang' => $id_kandang,
            'id_user' => $request->user()->id,
            'id_pakan' => $request->id_pakan,
            'jumlah_pakan' => $request->jumlah_pakan,
            'telur' => $request->telur ?? 0,
            'kematian' => $request->kematian ?? 0,
            'jumlah_sakit' => $request->jumlah_sakit ?? 0,
            'id_penyakit' => $request->id_penyakit,
        ]);

        // Tentukan apakah alert perlu ditampilkan
        $showAlert = $request->jumlah_sakit > 0 || $request->id_penyakit !== null;

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Laporan harian berhasil ditambahkan dan stok pakan diperbarui.',
            'show_alert' => $showAlert,
            'alert_message' => $showAlert 
                ? 'Penanganan unggas yang sakit dapat dilihat pada menu history penyakit.' 
                : null,
            'data' => [
                'laporan' => $laporan,
                'pengobatan' => $pengobatan, // Mengembalikan pengobatan di response
                'nama_penyakit' => $nama_penyakit, // Mengembalikan nama penyakit
                'deskripsi_penyakit' => $deskripsi_penyakit, // Mengembalikan nama penyakit
                'jenis_pakan' => $jenis_pakan,
                'nama_pakan' => $nama_pakan,
            ],
        ], Response::HTTP_CREATED);
    }

    // Menampilkan semua laporan harian untuk admin
    public function index()
    {
        // Ambil semua laporan dengan informasi terkait, seperti nama kandang dan user
        $laporans = LaporanHarian::with(['kandang', 'user', 'penyakit', 'pakan'])
        ->orderBy('created_at', 'DESC') 
        ->get();

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Laporan harian berhasil diambil.',
            'data' => $laporans,
        ]);
    }
}
