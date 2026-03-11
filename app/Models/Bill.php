<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = ['booking_id', 'amount', 'due_date', 'status'];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function payments() { return $this->hasMany(Payment::class); }

    // Accessor: Format Jatuh Tempo ($nextBill->due_date_formatted)
    public function getDueDateFormattedAttribute()
    {
        return Carbon::parse($this->due_date)->translatedFormat('d M Y');
    }

    // Accessor: Periode Mulai ($nextBill->periode_start)
    public function getPeriodeStartAttribute()
    {
        return Carbon::parse($this->due_date)->subMonth()->translatedFormat('d M');
    }

    // Accessor: Periode Selesai ($nextBill->periode_end)
    public function getPeriodeEndAttribute()
    {
        return Carbon::parse($this->due_date)->translatedFormat('d M Y');
    }
}