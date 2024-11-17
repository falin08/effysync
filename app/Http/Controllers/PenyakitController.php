<?php

namespace App\Http\Controllers;
use App\Models\Penyakit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Cast\String_;

class PenyakitController extends Controller
{
     // Mengambil semua data penyakit dari tabel "penyakits"
    public function index()
    {
        $penyakits = Penyakit::all();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => "success",
            'data' => $penyakits,
        ]);
    }
    
    // Menambahkan penyakit baru
    public function create(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'gejala' => 'required|string',
            'pengobatan' => 'required|string',
        ]);

        $penyakit = Penyakit::create($request->all());

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Penyakit berhasil ditambahkan.',
            'data' => $penyakit,
        ], Response::HTTP_CREATED);
    }

     // Menampilkan detail penyakit berdasarkan ID
     public function show($id)
     {
         $penyakit = Penyakit::findOrFail($id);
 
         return response()->json([
             'status' => Response::HTTP_OK,
             'message' => 'Success',
             'data' => $penyakit,
         ]);
     }

    // Mengupdate data penyakit
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'sometimes|string|max:255',
            'deskripsi' => 'sometimes|string',
            'gejala' => 'sometimes|string',
            'pengobatan' => 'sometimes|string',
        ]);

        $penyakit = Penyakit::findOrFail($id);
        $penyakit->update($request->all());

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Penyakit berhasil diperbarui.',
            'data' => $penyakit,
        ]);
    }

    // Menghapus penyakit
    public function delete($id)
    {
        $penyakit = Penyakit::findOrFail($id);
        $penyakit->delete();

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Penyakit berhasil dihapus.',
        ]);
    }

    // Endpoint untuk mendapatkan seluruh gejala unik
    public function getGejala()
    {
        $penyakit = Penyakit::select('id', 'gejala')->distinct()->get();

        return response()->json([
            'status' => Response::HTTP_OK,
            'data' => $penyakit
        ]);
    }
}
