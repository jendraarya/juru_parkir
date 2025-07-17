<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasFactory;

    protected $table = 'pemasukan';

    protected $fillable = [
        'tiket_id',
        'jumlah',
        'tanggal',
        'keterangan',
    ];

    public $timestamps = false;


    // Relasi ke model TiketParkir (bukan Tiket aja)
    public function tiket()
    {
        return $this->belongsTo(Tiket::class, 'tiket_id');
    }
}