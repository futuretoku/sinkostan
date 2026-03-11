<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            // Menambahkan room_id setelah user_id
            // Kita pakai unsignedBigInteger karena ini adalah Foreign Key
            $table->unsignedBigInteger('room_id')->nullable()->after('user_id');
            
            // Opsional: Jika ingin buat relasi resmi ke tabel rooms
            // $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropColumn('room_id');
        });
    }
};