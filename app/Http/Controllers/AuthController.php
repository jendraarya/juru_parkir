<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
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

    return response()->json([
        'pesan' => 'Register berhasil',
        'data'  => [
            'id'           => $user->id,
            'nama'         => $user->nama,
            'email'        => $user->email,
            'lokasi_nama'  => optional($user->lokasi)->nama_lokasi,
            'created_at'   => $user->created_at,
        ]
    ], 201);
}


    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['pesan' => 'Email atau password salah'], 401);
        }

        return response()->json(['pesan' => 'Login berhasil', 'data' => $user]);
    }
}