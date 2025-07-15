<?php

namespace App\Http\Controllers;

use App\Models\JenisKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JenisKendaraanController extends Controller
{
    public function index()
    {
        return response()->json(['data' => JenisKendaraan::all()], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_jenis' => 'required|string|max:50',
            'tarif'      => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jenis = JenisKendaraan::create([
            'nama_jenis' => $request->nama_jenis,
            'tarif' => $request->tarif
        ]);

        return response()->json(['pesan' => 'Jenis kendaraan berhasil ditambahkan', 'data' => $jenis], 201);
    }

    public function destroy($id)
    {
        $jenis = JenisKendaraan::find($id);
        if (!$jenis) {
            return response()->json(['pesan' => 'Jenis kendaraan tidak ditemukan'], 404);
        }

        $jenis->delete();
        return response()->json(['pesan' => 'Jenis kendaraan berhasil dihapus'], 200);
    }
}