<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kost extends Model
{
    protected $fillable = ['name', 'address','location_link', 'image','price_min','price_max','description'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}

