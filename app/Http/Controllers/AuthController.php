<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->identifier)
        ->orWhere('username', $request->identifier)
        ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => Response::HTTP_UNAUTHORIZED, 'message' => 'Invalid credentials']);
        }

        $token = $user->createToken('user_login')->plainTextToken;

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => [
                'token' => $token,
                'user' => $user,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Logged out successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Logout failed: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function checkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        // Check if the email exists in the database
        $emailExists = User::where('email', $request->email)->exists();

        if ($emailExists) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Email already exists',
                'isExists' => true
            ]);
        } else {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Email does not exist, you can proceed with registration',
                'exists' => false
            ]);
        }
    }

    public function register(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:3',
                'username' => 'required|string|max:255|unique:users',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user',
                'username' => $request->username,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);


            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'User Registered Success',
                'data' => [
                    'user' => $user,
                ]
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Database error: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An error occurred during registration: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function registerAdmin(Request $request)
    {
        try{
            // Validasi input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:3',
                'username' => 'required|string|max:255|unique:users',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], Response::HTTP_BAD_REQUEST);
            }

            // Buat admin baru
            $admin = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'admin', // Tetapkan role sebagai admin
                'email_verified_at' => now(),
            ]);

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Admin created successfully',
                'data' => $admin,
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Database error: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An error occurred during registration: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showProfile()
    {
        // Mendapatkan data user yang sedang login
        $user = Auth::user();

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Profile data fetched successfully',
            'data' => $user, // Kirim data pengguna ke client
        ], Response::HTTP_OK);
    }

    public function editProfile(Request $request)
    {
        try{
            $userId = Auth::id();  // Mengambil ID pengguna yang login

            // Validasi input
            $validated = $request->validate([
                'username' => 'sometimes|string|max:255|unique:users,username,' . $userId,
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $userId,
            ]);

            // Mendapatkan user yang sedang login
            $user = User::findOrFail($userId);

            // Update data user
            $user->update($validated); // Menggunakan data yang sudah tervalidasi

            // Respon sukses
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Profile updated successfully',
                'data' => $user, // Mengembalikan user yang sudah diperbarui
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An error occurred while updating the profile: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
