<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    // Tambahkan baris sakti ini:
    protected $table = 'kosts';

    // Sebaiknya fillable diaktifkan agar tidak error saat input data nanti
    protected $fillable = [
        'name',
        'address',
        'location_link',
        'description',
        'image',
    ];

    public function rooms()
    {
        // Pastikan foreign key-nya adalah 'kost_id' sesuai file manajemen-kamar tadi
        return $this->hasMany(Room::class, 'kost_id');
    }
}