<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Tambahkan ini untuk Sanctum
use Illuminate\Database\Eloquent\Factories\HasFactory;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'lokasi_id',
    
    ];

    protected $hidden = [
        'password',
    ];

    public $timestamps = false;

    // Relasi: user belongs to lokasi
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    // Relasi: user has many tiket parkir
    public function tiketParkir()
    {
        return $this->hasMany(Tiket::class, 'juru_parkir_id');
    }
}