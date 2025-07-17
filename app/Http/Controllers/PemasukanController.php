<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pemasukan;
use Illuminate\Support\Facades\Auth;

class PemasukanController extends Controller
{
    // 1. Menampilkan semua data pemasukan milik user login
    public function index()
    {
        $userId = Auth::id();

        $data = Pemasukan::with('tiket')
            ->whereHas('tiket', function ($query) use ($userId) {
                $query->where('juru_parkir_id', $userId);
            })
            ->orderByDesc('tanggal')
            ->get();

        return response()->json($data);
    }

    // 2. Menambahkan pemasukan manual (hati-hati, pastikan tiket_id valid dan milik user login)
    public function store(Request $request)
    {
        $request->validate([
            'tiket_id' => 'nullable|exists:tiket_parkir,id',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $tiketId = $request->input('tiket_id');

        if ($tiketId) {
            $isOwned = \App\Models\Tiket::where('id', $tiketId)
                ->where('juru_parkir_id', Auth::id())
                ->exists();

            if (!$isOwned) {
                return response()->json(['message' => 'Tiket tidak ditemukan atau bukan milik Anda.'], 403);
            }
        }

        $pemasukan = Pemasukan::create($request->all());

        return response()->json([
            'message' => 'Pemasukan berhasil ditambahkan.',
            'data' => $pemasukan,
        ], 201);
    }

    // 3. Total pemasukan harian milik user login
    public function totalPemasukanHarian()
    {
        $userId = Auth::id();

        $total = Pemasukan::selectRaw('DATE(tanggal) as tanggal, SUM(jumlah) as total_pemasukan')
            ->whereHas('tiket', function ($query) use ($userId) {
                $query->where('juru_parkir_id', $userId);
            })
            ->groupByRaw('DATE(tanggal)')
            ->orderByDesc('tanggal')
            ->get();

        return response()->json($total);
    }

    // 4. Total seluruh pemasukan user login
    public function totalSemuaPemasukan()
    {
        $userId = Auth::id();

        $total = Pemasukan::whereHas('tiket', function ($query) use ($userId) {
            $query->where('juru_parkir_id', $userId);
        })->sum('jumlah');

        return response()->json(['total_semua_pemasukan' => $total]);
    }

    // 5. Total pemasukan hari ini milik user login
    public function pemasukanHariIni()
    {
        $userId = Auth::id();

        $total = Pemasukan::whereDate('tanggal', now()->toDateString())
            ->whereHas('tiket', function ($query) use ($userId) {
                $query->where('juru_parkir_id', $userId);
            })->sum('jumlah');

        return response()->json(['pemasukan_hari_ini' => $total]);
    }

    // 6. Jumlah transaksi pemasukan hari ini (jumlah karcis) milik user login
    public function jumlahTransaksiHariIni()
    {
        $userId = Auth::id();

        $jumlah = Pemasukan::whereDate('tanggal', now()->toDateString())
            ->whereHas('tiket', function ($query) use ($userId) {
                $query->where('juru_parkir_id', $userId);
            })->count();

        return response()->json(['jumlah_transaksi_hari_ini' => $jumlah]);
    }

    // 7. Detail pemasukan berdasarkan ID hanya milik user login
    public function show($id)
    {
        $userId = Auth::id();

        $pemasukan = Pemasukan::with('tiket')
            ->where('id', $id)
            ->whereHas('tiket', function ($query) use ($userId) {
                $query->where('juru_parkir_id', $userId);
            })
            ->first();

        if (!$pemasukan) {
            return response()->json(['pesan' => 'Pemasukan tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'Berhasil',
            'data'   => $pemasukan
        ], 200);
    }
}