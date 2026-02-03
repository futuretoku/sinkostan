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

Route::get('/', function () {
    return view('welcome');
});

// 1. PENGATUR REDIRECT
Route::get('/redirect', [RedirectController::class, 'index'])
    ->middleware('auth')
    ->name('redirect');

// 2. DASHBOARD USER
Route::get('/dashboard', [BranchController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 3. DETAIL CABANG
Route::get('/branch/{id}', [BranchController::class, 'show'])->name('branch.show');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/booking', [BookingController::class, 'store']);
    Route::get('/my-bills', [BillController::class, 'index']);
    Route::get('/bill/{bill}/pay', [BillController::class, 'pay'])->name('bill.pay');
    
    // Ini rute simpan bukti yang sudah support ID Booking maupun Bill
    Route::post('/bill/{bill}/pay', [BillController::class, 'storePayment'])->name('bill.pay.store');
    
    // Route Kirim Keluhan Maintenance untuk User
    Route::post('/maintenance/send', [MaintenanceController::class, 'store'])->name('maintenance.store');

    // FITUR TAMBAHAN: Riwayat Booking
    Route::get('/booking/history', [BookingController::class, 'history'])->name('booking.history');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Grup Terpadu)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/rooms/update-status', [AdminDashboardController::class, 'updateStatus'])->name('rooms.updateStatus');

        // Pembayaran & Verifikasi
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        // FITUR TAMBAHAN: Tombol Approve & Reject di sisi Admin
        Route::post('/payments/{id}/approve', [AdminPaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{id}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');

        // Manajemen Cabang & Kamar (Dipusatkan di AdminBranchRoomController)
        Route::get('/branches', [AdminBranchRoomController::class, 'index'])->name('branches.index');
        Route::post('/branches/store', [AdminBranchRoomController::class, 'storeKost'])->name('branches.store');
        Route::post('/rooms/store', [AdminBranchRoomController::class, 'storeRoom'])->name('rooms.store');

        // Penyewa & Tagihan
        Route::get('/tenants', [AdminTenantController::class, 'index'])->name('tenants.index');
        Route::get('/get-tenants/{kost_id}', [AdminTenantController::class, 'getTenantsByBranch']);
        Route::get('/invoices', [AdminBillController::class, 'index'])->name('invoices.index');
        Route::get('/get-bills/{kost_id}', [AdminBillController::class, 'getBillsByBranch']);

        // Notifikasi & Laporan
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');

        // Maintenance Admin
        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::put('/maintenance/{id}/update-status', [MaintenanceController::class, 'updateStatus'])->name('maintenance.update');
});

// Route Payment & Booking Luar (Tanpa Prefix Admin)
Route::get('/payment/{room_id}', [BookingController::class, 'showPayment'])->name('payment.show');
Route::post('/payment/process', [BookingController::class, 'processPayment'])->name('payment.process');
Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');

//Route buat payment room
Route::get('payment/{id}', [RoomController::class, 'showPayment'])->name('payment.show');

//Route buat bayar beneran
Route::get('/booking/payment-detail/{id}', [App\Http\Controllers\BookingController::class, 'showPaymentDetail'])->name('booking.payment_detail');


Route::get('/test-wa', function () {
    Http::post('http://localhost:3000/send-message', [
        'phone' => '6289506700308', // ganti nomor kamu
        'message' => 'maul ah ah ah'
    ]);

    return 'Pesan WA sudah dikirim!';
});

require __DIR__.'/auth.php';