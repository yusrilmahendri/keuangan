<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saldos', function (Blueprint $table) {
            $table->string('nota_image')->nullable()->after('periode_saldo');
        });
    }

    public function down(): void
    {
        Schema::table('saldos', function (Blueprint $table) {
            $table->dropColumn('nota_image');
        });
    }
};
