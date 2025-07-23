<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ✅ REGISTER
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6',
            'lokasi_id'  => 'required|exists:lokasi,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'pesan' => 'Input tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'nama'      => $request->nama,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'lokasi_id' => $request->lokasi_id,
        ]);

        $user->load('lokasi');

        // ✅ Buat token setelah registrasi (langsung login)
        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'pesan' => 'Register berhasil',
            'token' => $token,
            'data'  => [
                'id'           => $user->id,
                'nama'         => $user->nama,
                'email'        => $user->email,
                'lokasi_nama'  => optional($user->lokasi)->nama_lokasi,
            ]
        ], 201);
    }

    // ✅ LOGIN
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Login gagal, cek email dan password.'
            ], 401);
        }

        $user = Auth::user();
        $lokasi = Lokasi::find($user->lokasi_id);
        // dd($lokasi);
        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'lokasi_id' => $user->lokasi_id,
                'lokasi_nama' => $lokasi->nama_lokasi ?? '-',
            ]
        ]);
    }

    // ✅ LOGOUT (opsional)
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout berhasil.'
        ]);
    }
}