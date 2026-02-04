<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\AdminBillController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminTenantController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminBranchRoomController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

Route::get('/branch/{id}', [BranchController::class, 'show'])->name('branch.show');
Route::get('/payment/{id}', [RoomController::class, 'showPayment'])->name('payment.show');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES (Penyewa)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/redirect', [RedirectController::class, 'index'])->name('redirect');
    Route::get('/dashboard', [BranchController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/history', [BookingController::class, 'history'])->name('booking.history');
    Route::get('/booking/payment-detail/{id}', [BookingController::class, 'showPaymentDetail'])->name('booking.payment_detail');
    
    Route::get('/my-bills', [BillController::class, 'index'])->name('my-bills');
    Route::get('/bill/{bill}/pay', [BillController::class, 'pay'])->name('bill.pay');
    Route::post('/bill/{bill}/pay', [BillController::class, 'storePayment'])->name('bill.pay.store');

    Route::post('/maintenance/send', [MaintenanceController::class, 'store'])->name('maintenance.store');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Pemilik Kost)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        
        // 1. Dashboard Admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/rooms/update-status', [AdminDashboardController::class, 'updateStatus'])->name('rooms.updateStatus');

        // 2. Verifikasi Pembayaran (AdminPaymentController)
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/{id}/approve', [AdminPaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{id}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');

        // 3. Manajemen Cabang & Kamar
        Route::get('/branches', [AdminBranchRoomController::class, 'index'])->name('branches.index');
        Route::post('/branches/store', [AdminBranchRoomController::class, 'storeKost'])->name('branches.store');
        Route::post('/rooms/store', [AdminBranchRoomController::class, 'storeRoom'])->name('rooms.store');

        // 4. Penyewa (Tenants)
        Route::get('/tenants', [AdminTenantController::class, 'index'])->name('tenants.index');
        Route::get('/get-tenants/{kost_id}', [AdminTenantController::class, 'getTenantsByBranch']);

        // 5. Tagihan (Invoices) & Aksi Tagihan
        Route::get('/invoices', [AdminBillController::class, 'index'])->name('invoices.index');
        Route::post('/bill-reminder/{id}', [AdminBillController::class, 'sendReminder'])->name('bill.reminder');
        
        // FIX: Mengubah 'ByBranch' menjadi 'getBillsByBranch' agar sesuai dengan Controller
        Route::get('/get-bills/{kost_id}', [AdminBillController::class, 'getBillsByBranch']);
        
        // Route untuk aksi tombol (Konfirmasi & Hapus)
        Route::post('/bill-confirm/{id}', [AdminBillController::class, 'confirm'])->name('bill.confirm');
        Route::delete('/bill-delete/{id}', [AdminBillController::class, 'destroy'])->name('bill.destroy');

        // 6. Notifikasi & Laporan
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');

        // 7. Maintenance (Keluhan)
        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::put('/maintenance/{id}/update-status', [MaintenanceController::class, 'updateStatus'])->name('maintenance.update');
});

/*
|--------------------------------------------------------------------------
| TESTING & OTHERS
|--------------------------------------------------------------------------
*/
Route::get('/test-wa', function () {
    Http::post('http://localhost:3000/send-message', [
        'phone' => '6289506700308', 
        'message' => 'Test Pesan dari Sin Kost An'
    ]);
    return 'Pesan WA sudah dikirim!';
});

require __DIR__.'/auth.php';
