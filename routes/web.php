<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\AdminBillController;
use App\Http\Controllers\AdminBranchRoomController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminTenantController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MyRoomController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\RoomController;
use App\Models\Booking;
use App\Notifications\RentReminderNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('redirect');
    }
    return view('auth.login');
})->name('login_page');

Route::get('/branch/{id}', [BranchController::class, 'show'])->name('branch.show');
Route::get('/payment/{id}', [RoomController::class, 'showPayment'])->name('payment.show');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/redirect', [RedirectController::class, 'index'])->name('redirect');

    /* --- USER ROUTES (Penyewa) --- */
    Route::get('/dashboard', [BranchController::class, 'index'])->name('dashboard');
    
    // My Room Routes
    Route::get('/my-room', [MyRoomController::class, 'index'])->name('user.my-room.current');
    Route::get('/my-room/{userRoomId}', [MyRoomController::class, 'show'])->name('user.my-room');
    Route::post('/room/extend/{userRoomId}', [MyRoomController::class, 'extendRoom'])->name('extend.room');
    
    Route::prefix('profile')->as('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index'); 
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::get('/notifikasi', [UserNotificationController::class, 'index'])->name('user.notifications');

    Route::prefix('booking')->as('booking.')->group(function () {
        Route::post('/store', [BookingController::class, 'store'])->name('store');
        Route::get('/history', [BookingController::class, 'history'])->name('history');
        Route::get('/payment-detail/{id}', [BookingController::class, 'showPaymentDetail'])->name('payment_detail');
    });
    
    Route::get('/my-bills', [BillController::class, 'index'])->name('my-bills');
    Route::get('/bill/{bill}/pay', [BillController::class, 'pay'])->name('bill.pay');
    Route::post('/bill/{bill}/pay', [BillController::class, 'storePayment'])->name('bill.pay.store');

    Route::prefix('maintenance')->as('user.maintenance.')->group(function () {
        Route::get('/', [MaintenanceController::class, 'create'])->name('create');
        Route::post('/send', [MaintenanceController::class, 'store'])->name('store');
    });

    Route::put('/maintenance/{id}/update-status', [MaintenanceController::class, 'updateStatus'])
          ->name('admin.maintenance.update');
          
    Route::get('/maintenance/history', [MaintenanceController::class, 'history'])->name('user.maintenance.history');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Pemilik Kost)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {
    
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/rooms/update-status', [AdminDashboardController::class, 'updateStatus'])->name('rooms.updateStatus');

    Route::prefix('payments')->as('payments.')->group(function () {
        Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
        Route::post('/{id}/approve', [AdminPaymentController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [AdminPaymentController::class, 'reject'])->name('reject');
    });

    Route::get('/branches', [AdminBranchRoomController::class, 'index'])->name('branches.index');
    Route::post('/branches/store', [AdminBranchRoomController::class, 'storeKost'])->name('branches.store');
    Route::post('/rooms/store', [AdminBranchRoomController::class, 'storeRoom'])->name('rooms.store');
    Route::put('/rooms/{id}', [AdminBranchRoomController::class, 'updateRoom'])->name('rooms.update');

    Route::get('/tenants', [AdminTenantController::class, 'index'])->name('tenants.index');
    Route::get('/get-tenants/{kost_id}', [AdminTenantController::class, 'getTenantsByBranch']);
    Route::post('/update_tenant_status', [AdminTenantController::class, 'updateStatus'])->name('update_tenant_status');

    Route::get('/invoices', [AdminBillController::class, 'index'])->name('invoices.index');
    Route::get('/get-bills/{kost_id}', [AdminBillController::class, 'getBillsByBranch']);
    Route::post('/bill-reminder/{id}', [AdminBillController::class, 'sendReminder'])->name('bill.reminder');
    Route::post('/bill-confirm/{id}', [AdminBillController::class, 'confirm'])->name('bill.confirm');
    Route::delete('/bill-delete/{id}', [AdminBillController::class, 'destroy'])->name('bill.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    
    Route::get('/maintenance', [MaintenanceController::class, 'adminIndex'])->name('maintenance.index');    
    Route::delete('/delete-tenant/{id}', [AdminTenantController::class, 'deleteTenant']);

    Route::get('/laporan/keuangan', [AdminReportController::class, 'keuanganDetail'])->name('laporankeuangan-detail');
    Route::get('/laporan/kamar-penyewa-detail', [AdminReportController::class, 'kamarPenyewaDetail'])->name('laporankamardanpenyewa-detail');

    Route::post('/admin/payment/{id}/confirm/{status}', [AdminPaymentController::class, 'confirmPayment'])
     ->name('admin.payment.confirm');
});

/*
|--------------------------------------------------------------------------
| TESTING
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