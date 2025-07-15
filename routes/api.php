<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\JenisKendaraanController;

//Login and Register
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); // Tambahan register

// User
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

//Tiker Parkir
Route::get('/tiket', [TiketController::class, 'index']);
Route::post('/tiket', [TiketController::class, 'store']);
Route::get('/tiket/{id}', [TiketController::class, 'show']);
Route::put('/tiket/{id}', [TiketController::class, 'update']);
Route::delete('/tiket/{id}', [TiketController::class, 'destroy']);

// Lokasi
Route::get('/lokasi', [LokasiController::class, 'index']);
Route::post('/lokasi', [LokasiController::class, 'store']);
Route::delete('/lokasi/{id}', [LokasiController::class, 'destroy']);

// Jenis Kendaraan
Route::get('/jenis-kendaraan', [JenisKendaraanController::class, 'index']);
Route::post('/jenis-kendaraan', [JenisKendaraanController::class, 'store']);
Route::delete('/jenis-kendaraan/{id}', [JenisKendaraanController::class, 'destroy']);