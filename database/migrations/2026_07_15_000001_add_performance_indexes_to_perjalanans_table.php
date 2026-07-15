<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perjalanans', function (Blueprint $table) {
            $table->index('pegawai_id', 'idx_perjalanans_pegawai_id');
            $table->index('tanggal', 'idx_perjalanans_tanggal');
            $table->index('status_efisiensi', 'idx_perjalanans_status_efisiensi');
            $table->index('no_bon', 'idx_perjalanans_no_bon');
            $table->index('km_baru', 'idx_perjalanans_km_baru');
            $table->index('km_lama', 'idx_perjalanans_km_lama');
        });

        Schema::table('pegawais', function (Blueprint $table) {
            $table->index('nama', 'idx_pegawais_nama');
        });

        Schema::table('kendaraans', function (Blueprint $table) {
            $table->index('plat_nomor', 'idx_kendaraans_plat_nomor');
            $table->index('merk', 'idx_kendaraans_merk');
        });
    }

    public function down(): void
    {
        Schema::table('perjalanans', function (Blueprint $table) {
            $table->dropIndex('idx_perjalanans_pegawai_id');
            $table->dropIndex('idx_perjalanans_tanggal');
            $table->dropIndex('idx_perjalanans_status_efisiensi');
            $table->dropIndex('idx_perjalanans_no_bon');
            $table->dropIndex('idx_perjalanans_km_baru');
            $table->dropIndex('idx_perjalanans_km_lama');
        });

        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropIndex('idx_pegawais_nama');
        });

        Schema::table('kendaraans', function (Blueprint $table) {
            $table->dropIndex('idx_kendaraans_plat_nomor');
            $table->dropIndex('idx_kendaraans_merk');
        });
    }
};
