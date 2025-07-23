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
    $validator = Validator::make($request->all(), [
        'jenis_kendaraan_id' => 'required|exists:jenis_kendaraan,id',
        'tanggal'            => 'required|date',
        'tarif'              => 'required|integer',
        'juru_parkir_id'     => 'required|exists:users,id',
        'lokasi_id'          => 'required|exists:lokasi,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        // Format tanggal ke ddmmyyyy
        $tanggalFormatted = \Carbon\Carbon::parse($request->tanggal)->format('dmY');

        // Hitung jumlah tiket yang sudah ada di tanggal itu
        $jumlahHariIni = Tiket::where('tanggal', $request->tanggal)->count();

        // Nomor urut dengan 2 digit (misal 01, 02)
        $urutan = str_pad($jumlahHariIni + 1, 2, '0', STR_PAD_LEFT);

        // Gabungkan jadi nomor karcis akhir
        $nomorKarcis = '00' . $urutan . $tanggalFormatted;

        // Simpan tiket
        $tiket = Tiket::create([
            'nomor_karcis'       => $nomorKarcis,
            'jenis_kendaraan_id' => $request->jenis_kendaraan_id,
            'tanggal'            => $request->tanggal,
            'tarif'              => $request->tarif,
            'juru_parkir_id'     => $request->juru_parkir_id,
            'lokasi_id'          => $request->lokasi_id,
        ]);

        $tiket->load(['jenisKendaraan', 'juruParkir', 'lokasi']);


        // Simpan pemasukan otomatis
        Pemasukan::create([
            'tiket_id'   => $tiket->id,
            'jumlah'     => $request->tarif,
            'tanggal'    => $request->tanggal,
            'keterangan' => 'Pemasukan dari tiket ' . $nomorKarcis
        ]);

        return response()->json([
            'pesan' => 'Tiket berhasil dibuat',
            'data'  => $tiket
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'pesan' => 'Terjadi kesalahan saat menyimpan data',
            'error' => $e->getMessage()
        ], 500);
    }
}

// Mendapatkan nomor karcis baru
public function getNomorKarcisBaru()
{
    $tanggal = now()->format('Y-m-d');
    $tanggalFormatted = now()->format('dmY');

    $lastKarcis = Tiket::where('tanggal', $tanggal)
        ->orderByDesc('nomor_karcis')
        ->first();

    $nextUrutan = 1;
    if ($lastKarcis) {
        // Ambil 4 digit urutan dari depan (karcis format: 000112072025)
        $urutan = substr($lastKarcis->nomor_karcis, 0, 4);
        $nextUrutan = intval($urutan) + 1;
    }

    $formatted = str_pad($nextUrutan, 4, '0', STR_PAD_LEFT) . $tanggalFormatted;

    return response()->json(['nomor_karcis' => $formatted]);
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