<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LokasiController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Lokasi::all()], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lokasi' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $lokasi = Lokasi::create([
            'nama_lokasi' => $request->nama_lokasi
        ]);

        return response()->json(['pesan' => 'Lokasi berhasil ditambahkan', 'data' => $lokasi], 201);
    }

    public function destroy($id)
    {
        $lokasi = Lokasi::find($id);
        if (!$lokasi) {
            return response()->json(['pesan' => 'Lokasi tidak ditemukan'], 404);
        }

        $lokasi->delete();
        return response()->json(['pesan' => 'Lokasi berhasil dihapus'], 200);
    }
}