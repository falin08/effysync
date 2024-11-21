<?php

namespace App\Http\Controllers;
use App\Models\Pakan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PakanController extends Controller
{
     // Mengambil semua data pakan dari tabel "pakans"
    public function index()
    {
        $pakans = Pakan::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => "success",
            'data' => $pakans,
        ]);
    }
    
    // Menambahkan pakan baru
    public function create(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'stok' => 'required|numeric|min:0',
        ]);

        $pakan = Pakan::create($request->all());

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Pakan berhasil ditambahkan.',
            'data' => $pakan,
        ], Response::HTTP_CREATED);
    }

     // Menampilkan detail pakan berdasarkan ID
     public function show($id)
     {
         $pakan = Pakan::findOrFail($id);
 
         return response()->json([
             'status' => Response::HTTP_OK,
             'message' => 'Success',
             'data' => $pakan,
         ]);
     }

    // Mengupdate data pakan
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'sometimes|string|max:255',
            'jenis' => 'sometimes|string|max:255',
            'stok' => 'sometimes|numeric|min:0',
        ]);

        $pakan = Pakan::findOrFail($id);
        $pakan->update($request->all());

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Pakan berhasil diperbarui.',
            'data' => $pakan,
        ]);
    }

    // Menghapus pakan
    public function delete($id)
    {
        $pakan = Pakan::findOrFail($id);
        $pakan->delete();

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Pakan berhasil dihapus.',
        ]);
    }

    public function getAllPakan()
    {
        // Mengambil data pakan dan mengelompokkan berdasarkan jenis secara case-insensitive
        $pakanData = Pakan::selectRaw('LOWER(jenis) as jenis_lower, id, nama')
            ->orderBy('jenis_lower', 'ASC') // Mengurutkan berdasarkan jenis
            ->orderBy('nama', 'ASC') // Mengurutkan nama dalam setiap jenis
            ->get()
            ->groupBy('jenis_lower') // Mengelompokkan berdasarkan jenis_lower
            ->map(function ($items, $jenis) {
                return [
                    'jenis' => ucfirst($jenis), // Menampilkan jenis dengan huruf pertama kapital
                    'pakan' => $items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'nama' => $item->nama
                        ];
                    })
                ];
            })
            ->values(); // Menghapus kunci pengelompokan agar JSON lebih rapi

        return response()->json([
            'status' => Response::HTTP_OK,
            'data' => $pakanData
        ]);
    }
}
