<?php

namespace App\Http\Controllers;

use App\Models\LaporanHarian;
use App\Models\Pakan;
use App\Models\Kandang;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Telur Seluruh Kandang Aktif
        $totalTelur = LaporanHarian::whereHas('kandang', function ($query) {
            $query->where('status', 'aktif');
        })->sum('telur');

        // 2. Total Telur Per Hari
        $totalTelurPerHari = LaporanHarian::whereHas('kandang', function ($query) {
            $query->where('status', 'aktif');
        })->whereDate('created_at', now()->toDateString())->sum('telur');

        // 3. Diagram Pie: Ayam Sehat, Sakit, Mati
        $dataKesehatan = Kandang::join('laporan_harians', 'kandangs.id', '=', 'laporan_harians.id_kandang')
        ->where('kandangs.status', 'aktif')
        ->selectRaw('
            SUM(laporan_harians.jumlah_sakit) as sakit,
            SUM(laporan_harians.kematian) as mati,
            SUM(kandangs.jumlah_unggas) - SUM(laporan_harians.jumlah_sakit) - SUM(laporan_harians.kematian) as sehat
        ')
        ->first();

        $dataKesehatan = $dataKesehatan ? [
            'sehat' => $dataKesehatan->sehat ?? 0,
            'sakit' => $dataKesehatan->sakit ?? 0,
            'mati' => $dataKesehatan->mati ?? 0,
        ] : [
            'sehat' => 0,
            'sakit' => 0,
            'mati' => 0,
        ];

        // 4. Diagram Pie: Stok Pakan Seluruh Jenis
        $dataPakan = Pakan::selectRaw('LOWER(jenis) as jenis_lowercase, SUM(stok) as total_stok')
        ->groupBy('jenis_lowercase')
        ->get()
        ->map(function ($item) {
            return [
                'jenis' => ucfirst($item->jenis_lowercase), // Mengembalikan jenis dengan huruf besar di awal
                'total_stok' => $item->total_stok ?? 0,
            ];
        });

        // 5. Recent Laporan Harian
        $recentLaporan = LaporanHarian::with(['kandang', 'user', 'pakan'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($laporan) {
                return [
                    'tanggal_laporan' => $laporan->created_at,
                    'jumlah_telur' => $laporan->telur ?? 0,
                    'jumlah_sakit' => $laporan->jumlah_sakit ?? 0,
                    'jumlah_kematian' => $laporan->kematian ?? 0,
                    'jumlah_pakan' => $laporan->jumlah_pakan ?? 0,
                    'kandang' => $laporan->kandang->kode ?? 'N/A',
                    'user' => $laporan->user->name ?? 'N/A',
                    'pakan' => $laporan->pakan->jenis ?? 'N/A',
                ];
            });

        // 6. Pakan Digunakan Per Bulan Berdasarkan Jenis
        $pakanPerBulan = LaporanHarian::join('pakans', 'laporan_harians.id_pakan', '=', 'pakans.id')
            ->whereMonth('laporan_harians.created_at', now()->month)
            ->select(
                DB::raw('LOWER(pakans.jenis) as jenis_lowercase'), 
                DB::raw('SUM(laporan_harians.jumlah_pakan) as total_pakan')
            )
            ->groupBy('jenis_lowercase')
            ->get()
            ->map(function ($item) {
                return [
                    'jenis' => ucfirst($item->jenis_lowercase), // Memformat jenis pakan dengan huruf besar di awal
                    'total_pakan' => $item->total_pakan ?? 0,
                ];
            });

        // Response Data
        return response()->json([
            'status' => Response::HTTP_OK,
            'data' => [
                'total_telur' => $totalTelur,
                'total_telur_per_hari' => $totalTelurPerHari,
                'diagram_kesehatan' => $dataKesehatan,
                'diagram_pakan' => $dataPakan,
                'recent_laporan' => $recentLaporan,
                'pakan_per_bulan' => $pakanPerBulan,
                'bulan' => now()->translatedFormat('F'),  // Nama bulan sekarang
            ],
        ], Response::HTTP_OK);
    }
}