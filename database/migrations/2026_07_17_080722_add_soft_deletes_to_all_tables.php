<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('kendaraans', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('perjalanans', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('kendaraans', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('perjalanans', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
