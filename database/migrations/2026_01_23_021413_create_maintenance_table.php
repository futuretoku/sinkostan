<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('maintenances', function (Blueprint $table) {
        $table->id(); // ID Primary Key
        $table->string('judul');
        $table->string('kategori');
        $table->text('deskripsi');
        $table->string('nomor_kamar');
        $table->string('status')->default('Dalam Proses');
        $table->timestamps(); // Ini akan membuat kolom created_at dan updated_at
    });
}

    public function down()
    {
        Schema::dropIfExists('maintenances');
    }
};