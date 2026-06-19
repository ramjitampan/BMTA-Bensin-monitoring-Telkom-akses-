<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PERBAIKAN ARSITEKTUR:
        // Sebelumnya 1 baris = 1 transaksi isi BBM, dengan km_akhir nullable
        // yang baru diisi belakangan dengan cara "nyari trip lain yang masih
        // terbuka". Itu rawan salah pasangan kalau input gak berurutan tanggal.
        //
        // Sekarang 1 baris = 1 periode pemakaian kendaraan (sama seperti
        // rekap manual di kertas): km_lama & km_baru ada di baris yang sama,
        // jadi selisih & efisiensi langsung kehitung saat itu juga, tanpa
        // perlu nyambung ke record lain.
        Schema::create('perjalanans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pegawai_id')
                  ->constrained('pegawais')
                  ->cascadeOnDelete();

            $table->foreignId('kendaraan_id')
                  ->constrained('kendaraans')
                  ->cascadeOnDelete();

            $table->date('tanggal');
            $table->string('tujuan');
            $table->string('uraian')->nullable();

            // Odometer dicatat langsung dalam satu baris, sama seperti kertas
            $table->decimal('km_lama', 10, 2);
            $table->decimal('km_baru', 10, 2);
            // jarak = km_baru - km_baru, disimpan biar gak perlu dihitung ulang
            // tiap query, tapi nilainya selalu hasil generated dari 2 kolom di atas.
            $table->decimal('jarak', 10, 2);

            $table->decimal('vol_liter', 10, 2);
            $table->decimal('harga_per_liter', 10, 2);
            $table->decimal('jumlah_biaya', 15, 2);

            $table->string('no_bon')->nullable();
            $table->string('foto_bon')->nullable();

            // efisiensi = jarak / vol_liter
            $table->decimal('efisiensi', 10, 2);
            $table->enum('status_efisiensi', ['balance', 'boros', 'anomali']);

            $table->timestamps();

            // Cegah duplikat input untuk kendaraan & tanggal yang sama
            $table->index(['kendaraan_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perjalanans');
    }
};
