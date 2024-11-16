<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanHarian;
use App\Models\Pakan;
use App\Models\Kandang;
use Illuminate\Http\Response;

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
        $dataKesehatan = LaporanHarian::whereHas('kandang', function ($query) {
            $query->where('status', 'aktif');
        })
        ->selectRaw('SUM(jumlah_sakit) as sakit, SUM(kematian) as mati, SUM(jumlah_pakan) - SUM(jumlah_sakit) - SUM(kematian) as sehat')
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
        $dataPakan = Pakan::selectRaw('jenis, SUM(stok) as total_stok')
            ->groupBy('jenis')
            ->get()
            ->map(function ($item) {
                return [
                    'jenis' => $item->jenis,
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
                    'tanggal_laporan' => $laporan->created_at->format('Y-m-d'),
                    'jumlah_telur' => $laporan->telur ?? 0,
                    'jumlah_sakit' => $laporan->jumlah_sakit ?? 0,
                    'jumlah_kematian' => $laporan->kematian ?? 0,
                    'jumlah_pakan' => $laporan->jumlah_pakan ?? 0,
                    'kandang' => $laporan->kandang->kode ?? 'N/A',
                    'user' => $laporan->user->name ?? 'N/A',
                    'pakan' => $laporan->pakan->jenis ?? 'N/A',
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
            ],
        ], Response::HTTP_OK);
    }
}