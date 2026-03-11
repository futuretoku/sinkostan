<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['Aktif', 'Non-Aktif', 'Expired'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_rooms');
    }
};