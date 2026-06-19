<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perjalanans', function (Blueprint $table) {
            if (!Schema::hasColumn('perjalanans', 'fraud_score')) {
                $table->unsignedSmallInteger('fraud_score')->default(0);
            }

            if (!Schema::hasColumn('perjalanans', 'fraud_flags')) {
                $table->json('fraud_flags')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('perjalanans', function (Blueprint $table) {
            if (Schema::hasColumn('perjalanans', 'fraud_flags')) {
                $table->dropColumn('fraud_flags');
            }

            if (Schema::hasColumn('perjalanans', 'fraud_score')) {
                $table->dropColumn('fraud_score');
            }
        });
    }
};
