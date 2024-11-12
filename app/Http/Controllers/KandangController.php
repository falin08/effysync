<?php

namespace App\Http\Controllers;
use App\Models\Kandang;
use App\Models\User;
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
     // Mengambil semua data kandang yang aktif untuk pengguna
    //  public function indexUser()
    //  {
    //      $kandangs = Kandang::where('status', 'aktif')->get();
    //      return response()->json([
    //          'status' => Response::HTTP_OK,
    //          'message' => "success",
    //          'data' => $kandangs,
    //      ]);
    //  }
 
    //  // Mengambil semua data kandang untuk admin (aktif dan tidak aktif)
    //  public function indexAdmin()
    //  {
    //      $kandangs = Kandang::all();
    //      return response()->json([
    //          'status' => Response::HTTP_OK,
    //          'message' => "success",
    //          'data' => $kandangs,
    //      ]);
    //  }
    
    // Menambahkan kandang baru
    public function create(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:255|unique:kandangs,kode',
            'jumlah_unggas' => 'required|integer|min:1',
            'jenis_unggas' => 'required|string',
            'status' => 'required|in:aktif,tak aktif',
        ]);

        // Set deactivated_at menjadi null saat membuat kandang baru
        $kandang = Kandang::create(array_merge($request->all(), ['deactivated_at' => null]));

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
            $kandang->status = 'tidak aktif'; // Mengupdate status
        } else {
            $kandang->deactivated_at = null; // Reset jika diaktifkan kembali
            $kandang->status = 'aktif'; // Mengupdate status
        }

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

}
