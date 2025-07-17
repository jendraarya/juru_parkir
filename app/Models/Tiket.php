<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    protected $table = 'tiket_parkir';

    protected $fillable = [
        'nomor_karcis',
        'jenis_kendaraan_id',
        'tanggal',
        'tarif',
        'juru_parkir_id',
        'lokasi_id',
    ];

    public $timestamps = false;

    // Relasi: Tiket belongsTo JenisKendaraan
    public function jenisKendaraan()
    {
        return $this->belongsTo(JenisKendaraan::class, 'jenis_kendaraan_id');
    }

    // Relasi: Tiket belongsTo User (juru parkir)
    public function juruParkir()
    {
        return $this->belongsTo(User::class, 'juru_parkir_id');
    }

    // Relasi: Tiket belongsTo Lokasi
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    // Relasi: Tiket hasOne Pemasukan
    public function pemasukan()
{
    return $this->hasOne(Pemasukan::class, 'tiket_id');
}

}