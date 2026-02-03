<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    // Nama tabel di database (Opsional jika namanya sudah sesuai jamak 'maintenances')
    protected $table = 'maintenances';

    // Kolom mana saja yang boleh diisi oleh user atau sistem
    protected $fillable = [
        'judul',
        'kategori',
        'deskripsi',
        'nomor_kamar',
        'status',
    ];

    // Jika kamu ingin memberikan nilai default untuk status saat data baru dibuat
    protected $attributes = [
        'status' => 'Dalam Proses',
    ];
}