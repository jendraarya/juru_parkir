<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('tiket_parkir', function (Blueprint $table) {
        $table->id();
        $table->string('nomor_karcis', 50);
        $table->unsignedBigInteger('jenis_kendaraan_id');
        $table->date('tanggal');
        $table->unsignedBigInteger('lokasi_id');
        $table->unsignedBigInteger('juru_parkir_id');
        $table->integer('tarif');
        $table->timestamps();

        $table->foreign('jenis_kendaraan_id')->references('id')->on('jenis_kendaraan')->onDelete('cascade');
        $table->foreign('lokasi_id')->references('id')->on('lokasi')->onDelete('cascade');
        $table->foreign('juru_parkir_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiket_parkir');
    }
};