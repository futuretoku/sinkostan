<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $blueprint) {
            // Kita tambah kolom biaya setelah kolom deskripsi
            // Default 0 agar data yang sudah ada tidak error
            $blueprint->integer('biaya')->default(0)->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $blueprint) {
            $blueprint->dropColumn('biaya');
        });
    }
};