<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use App\Models\JenisKendaraan;
use App\Models\User;
use App\Models\Lokasi;
use App\Models\Pemasukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TiketController extends Controller
{
    // 1. Tampilkan semua tiket
    public function index()
    {
        $tiket = Tiket::with(['jenisKendaraan', 'juruParkir', 'lokasi'])->get();
        return response()->json(['data' => $tiket], 200);
    }

    // 2. Simpan tiket baru & catat pemasukan otomatis
public function store(Request $request)
{
    // Validasi input tanpa nomor_karcis
    $validator = Validator::make($request->all(), [
        'jenis_kendaraan_nama' => 'required|string',
        'tanggal'              => 'required|date',
        'tarif'                => 'required|integer',
        'juru_parkir_nama'     => 'required|string',
        'lokasi_nama'          => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
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

        // Generate nomor karcis otomatis berdasarkan tanggal
        $lastKarcis = Tiket::where('tanggal', $request->tanggal)
            ->orderByDesc('nomor_karcis')
            ->first();

        $nextKarcis = $lastKarcis ? intval($lastKarcis->nomor_karcis) + 1 : 1;

        // Simpan tiket baru
        $tiket = Tiket::create([
            'nomor_karcis'       => (string) $nextKarcis,
            'jenis_kendaraan_id' => $jenis->id,
            'tanggal'            => $request->tanggal,
            'tarif'              => $request->tarif,
            'juru_parkir_id'     => $juru->id,
            'lokasi_id'          => $lokasi->id,
        ]);

        // Simpan pemasukan
        Pemasukan::create([
            'tiket_id'   => $tiket->id,
            'jumlah'     => $request->tarif,
            'tanggal'    => $request->tanggal,
            'keterangan' => 'Pemasukan dari tiket ' . $nextKarcis
        ]);

        return response()->json([
            'pesan' => 'Tiket dan pemasukan berhasil dibuat',
            'data'  => $tiket
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'pesan' => 'Terjadi kesalahan saat menyimpan data',
            'error' => $e->getMessage()
        ], 500);
    }
}


    // 3. Tampilkan detail tiket
 public function show($id)
{
    $tiket = Tiket::with(['jenisKendaraan', 'juruParkir', 'lokasi'])->find($id);

    if (!$tiket) {
        return response()->json(['pesan' => 'Tiket tidak ditemukan'], 404);
    }

    return response()->json([
        'status' => 'Berhasil',
        'data' => [
            'nomor_karcis'     => $tiket->nomor_karcis,
            'jenis_kendaraan'  => $tiket->jenisKendaraan->nama_jenis ?? '-',
            'tanggal'          => $tiket->tanggal,
            'waktu'            => date('H:i:s', strtotime($tiket->created_at)), // pastikan kolom ini ada
            'lokasi'           => $tiket->lokasi->nama_lokasi ?? '-',
            'juru_parkir_nama' => $tiket->juruParkir->nama ?? '-',
            'tarif'            => $tiket->tarif,
        ]
    ], 200);
}


    // 4. Update tiket (optional)
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

    // 5. Hapus tiket
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