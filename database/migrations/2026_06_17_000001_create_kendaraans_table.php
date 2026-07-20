<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('kendaraans', function (Blueprint $table) {
        $table->id();
        $table->string('plat_nomor')->unique();
        $table->string('merk');
        $table->enum('tipe', ['R4']);
        $table->year('tahun');
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
};
