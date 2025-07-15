<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // ✅ GET semua user (tampilkan nama lokasi, sembunyikan lokasi_id)
    public function index()
    {
        $users = User::with('lokasi')->get();

        $data = $users->map(function ($user) {
            return [
                'id'           => $user->id,
                'nama'         => $user->nama,
                'email'        => $user->email,
                'created_at'   => $user->created_at,
                'lokasi_nama'  => optional($user->lokasi)->nama_lokasi,
            ];
        });

        return response()->json(['data' => $data], 200);
    }

    // ✅ POST user baru (lokasi disimpan via ID, ditampilkan via nama)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email|max:100',
            'password'   => 'required|string|min:6',
            'lokasi_id'  => 'required|exists:lokasi,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'nama'       => $request->nama,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'lokasi_id'  => $request->lokasi_id,
        ]);

        $user->load('lokasi');

        return response()->json([
            'pesan' => 'Juru parkir berhasil dibuat',
            'data'  => [
                'id'           => $user->id,
                'nama'         => $user->nama,
                'email'        => $user->email,
                'created_at'   => $user->created_at,
                'lokasi_nama'  => optional($user->lokasi)->nama_lokasi,
            ]
        ], 201);
    }

    // ✅ GET detail user
    public function show($id)
    {
        $user = User::with('lokasi')->find($id);

        if (!$user) {
            return response()->json(['pesan' => 'Juru parkir tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => [
                'id'           => $user->id,
                'nama'         => $user->nama,
                'email'        => $user->email,
                'created_at'   => $user->created_at,
                'lokasi_nama'  => optional($user->lokasi)->nama_lokasi,
            ]
        ], 200);
    }

    // ✅ UPDATE user
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['pesan' => 'Juru parkir tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama'       => 'sometimes|string|max:100',
            'email'      => 'sometimes|email|unique:users,email,' . $id,
            'password'   => 'nullable|string|min:6',
            'lokasi_id'  => 'sometimes|exists:lokasi,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update data
        if ($request->has('lokasi_id')) {
            $user->lokasi_id = $request->lokasi_id;
        }

        $user->nama = $request->input('nama', $user->nama);
        $user->email = $request->input('email', $user->email);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        $user->load('lokasi');

        return response()->json([
            'pesan' => 'Juru parkir berhasil diperbarui',
            'data'  => [
                'id'           => $user->id,
                'nama'         => $user->nama,
                'email'        => $user->email,
                'created_at'   => $user->created_at,
                'lokasi_nama'  => optional($user->lokasi)->nama_lokasi,
            ]
        ], 200);
    }

    // ✅ DELETE user
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['pesan' => 'Juru parkir tidak ditemukan'], 404);
        }

        $user->delete();
        return response()->json(['pesan' => 'Juru parkir berhasil dihapus'], 200);
    }
}