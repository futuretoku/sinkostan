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
        'user_id',
        'kost_id',
        'judul',
        'kategori',
        'deskripsi',
        'nomor_kamar',
        'status',
        'foto',
    ];

    // Jika kamu ingin memberikan nilai default untuk status saat data baru dibuat
    protected $attributes = [
        'status' => 'Dalam Proses',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kost()
    {
        return $this->belongsTo(Kost::class, 'kost_id');
    }
}