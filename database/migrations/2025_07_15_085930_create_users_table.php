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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('nama', 100);
        $table->string('email', 100)->unique();
        $table->string('password');
        $table->unsignedBigInteger('lokasi_id');
        $table->timestamps();

        $table->foreign('lokasi_id')->references('id')->on('lokasi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};