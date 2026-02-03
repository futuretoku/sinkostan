<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Bill;


class SendBillingWa extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'billing:send-wa';

    /**
     * The console command description.
     */
    protected $description = 'Kirim pesan WA tagihan kost (TEST)';

    /** 
     * Execute the console command.
     */


public function handle()
{
    // tanggal hari ini + 7 hari
    $targetDate = Carbon::now()->addDays(7)->toDateString();

    $bills = Bill::where('status', 'unpaid')
        ->whereDate('due_date', $targetDate)
        ->get();

    foreach ($bills as $bill) {

        $message = "Halo {$bill->user->name} 👋\n\n"
            . "Ini pengingat bahwa sewa kamar kamu akan jatuh tempo *7 hari lagi* 🙏\n\n"
            . "💰 Tagihan: Rp " . number_format($bill->amount, 0, ',', '.') . "\n"
            . "📅 Jatuh tempo: " . Carbon::parse($bill->due_date)->translatedFormat('d F Y') . "\n\n"
            . "Mohon disiapkan ya. Terima kasih 🤍";

        Http::post('http://localhost:3000/send-message', [
            'phone' => $bill->user->phone,
            'message' => $message,
        ]);

        // ⛔ WAJIB delay biar aman
        sleep(3);
    }

    $this->info('Reminder H-7 berhasil dikirim.');
}

}
