<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // Step 1: Ubah kolom jadi STRING dulu biar data lama nggak marah
    Schema::table('bookings', function (Blueprint $table) {
        $table->string('status')->change();
    });

    // Step 2: Bersihkan data lama (Opsional tapi aman)
    // Semua yang aneh-aneh kita reset ke 'unpaid'
    DB::table('bookings')->whereNotIn('status', ['unpaid', 'pending', 'booked', 'paid', 'rejected'])
                         ->update(['status' => 'unpaid']);

    // Step 3: Baru kita kunci balik ke ENUM
    Schema::table('bookings', function (Blueprint $table) {
        $table->enum('status', ['unpaid', 'pending', 'booked', 'paid', 'rejected'])
              ->default('unpaid')
              ->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Kembalikan ke enum lama jika di-rollback (sesuaikan dengan enum aslimu)
            $table->string('status')->change(); 
        });
    }
};
