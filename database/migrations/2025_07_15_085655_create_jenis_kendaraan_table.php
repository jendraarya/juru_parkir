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
        // Cek apakah tabel belum ada, agar tidak membuat ulang
        if (!Schema::hasTable('jenis_kendaraan')) {
            Schema::create('jenis_kendaraan', function (Blueprint $table) {
                $table->id();
                $table->string('nama_jenis', 100)->unique(); // contoh: Motor, Mobil
                $table->decimal('tarif_per_jam', 10, 2)->default(0); // tarif parkir per jam
                $table->timestamps(); // created_at dan updated_at
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hanya hapus jika tabelnya benar-benar ada
        if (Schema::hasTable('jenis_kendaraan')) {
            Schema::dropIfExists('jenis_kendaraan');
        }
    }
};