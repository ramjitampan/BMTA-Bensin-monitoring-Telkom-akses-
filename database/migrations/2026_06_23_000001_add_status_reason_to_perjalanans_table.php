<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perjalanans', function (Blueprint $table) {
            $table->string('status_reason')->nullable()->after('status_efisiensi');
        });
    }

    public function down(): void
    {
        Schema::table('perjalanans', function (Blueprint $table) {
            $table->dropColumn('status_reason');
        });
    }
};
