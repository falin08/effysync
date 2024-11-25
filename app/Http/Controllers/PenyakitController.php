<?php

namespace App\Http\Controllers;
use App\Models\Penyakit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class PenyakitController extends Controller
{
     // Mengambil semua data penyakit dari tabel "penyakits"
    public function index()
    {
        $penyakits = Penyakit::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => "success",
            'data' => $penyakits,
        ]);
    }
    
    // Menambahkan penyakit baru
    public function create(Request $request)
    {
        try{
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
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

     // Menampilkan detail penyakit berdasarkan ID
     public function show($id)
     {
        try{
            $penyakit = Penyakit::findOrFail($id);
    
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $penyakit,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Penyakit tidak ditemukan.',
            ], Response::HTTP_NOT_FOUND);
        }
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
        try{
            $penyakit = Penyakit::findOrFail($id);
            $penyakit->delete();

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Penyakit berhasil dihapus.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Penyakit tidak ditemukan.',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Terjadi kesalahan saat menghapus penyakit.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
