<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use App\Models\JenisKendaraan;
use App\Models\User;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TiketController extends Controller
{
    // ✅ 1. Tampilkan semua tiket dengan relasi lengkap
    public function index()
    {
        $tiket = Tiket::with(['jenisKendaraan', 'juruParkir', 'lokasi'])->get();
        return response()->json(['data' => $tiket], 200);
    }

    // ✅ 2. Simpan tiket baru berdasarkan input NAMA (bukan ID)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_karcis'         => 'required|string|max:50|unique:tiket_parkir,nomor_karcis',
            'jenis_kendaraan_nama' => 'required|string',
            'tanggal'              => 'required|date',
            'tarif'                => 'required|integer',
            'juru_parkir_nama'     => 'required|string',
            'lokasi_nama'          => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cari ID berdasarkan nama
        $jenis  = JenisKendaraan::where('nama_jenis', $request->jenis_kendaraan_nama)->first();
        $juru   = User::where('nama', $request->juru_parkir_nama)->first();
        $lokasi = Lokasi::where('nama_lokasi', $request->lokasi_nama)->first();

        if (!$jenis || !$juru || !$lokasi) {
            return response()->json([
                'pesan' => 'Data tidak valid',
                'detail' => [
                    'jenis_kendaraan' => $jenis ? '✔' : '❌ Tidak ditemukan',
                    'juru_parkir'     => $juru ? '✔' : '❌ Tidak ditemukan',
                    'lokasi'          => $lokasi ? '✔' : '❌ Tidak ditemukan'
                ]
            ], 404);
        }

        // Simpan tiket
        $tiket = Tiket::create([
            'nomor_karcis'       => $request->nomor_karcis,
            'jenis_kendaraan_id' => $jenis->id,
            'tanggal'            => $request->tanggal,
            'tarif'              => $request->tarif,
            'juru_parkir_id'     => $juru->id,
            'lokasi_id'          => $lokasi->id,
        ]);

        return response()->json(['pesan' => 'Tiket berhasil dibuat', 'data' => $tiket], 201);
    }

    // ✅ 3. Detail tiket berdasarkan ID
    public function show($id)
    {
        $tiket = Tiket::with(['jenisKendaraan', 'juruParkir', 'lokasi'])->find($id);

        if (!$tiket) {
            return response()->json(['pesan' => 'Tiket tidak ditemukan'], 404);
        }

        return response()->json(['data' => $tiket], 200);
    }

    // ✅ 4. Update tiket (opsional, masih pakai ID)
    public function update(Request $request, $id)
    {
        $tiket = Tiket::find($id);

        if (!$tiket) {
            return response()->json(['pesan' => 'Tiket tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nomor_karcis'       => 'sometimes|string|max:50|unique:tiket_parkir,nomor_karcis,' . $id,
            'jenis_kendaraan_id' => 'sometimes|exists:jenis_kendaraan,id',
            'tanggal'            => 'sometimes|date',
            'tarif'              => 'sometimes|integer',
            'juru_parkir_id'     => 'sometimes|exists:users,id',
            'lokasi_id'          => 'sometimes|exists:lokasi,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tiket->update($request->all());

        return response()->json(['pesan' => 'Tiket berhasil diperbarui', 'data' => $tiket], 200);
    }

    // ✅ 5. Hapus tiket
    public function destroy($id)
    {
        $tiket = Tiket::find($id);

        if (!$tiket) {
            return response()->json(['pesan' => 'Tiket tidak ditemukan'], 404);
        }

        $tiket->delete();

        return response()->json(['pesan' => 'Tiket berhasil dihapus'], 200);
    }
}