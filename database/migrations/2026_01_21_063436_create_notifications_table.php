<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Terhubung ke tabel users
            $table->string('title')->nullable();
            $table->text('message'); // Untuk isi pesan notifikasi
            $table->boolean('is_read')->default(false); // Untuk cek apakah sudah dibaca
            $table->timestamps(); // Ini otomatis membuat created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};