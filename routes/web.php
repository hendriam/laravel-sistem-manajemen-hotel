<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ReportReservationController;
use App\Http\Controllers\ReportPaymentController;

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/', [AuthController::class, 'authenticate']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('profile/ganti-password/{id}', [ProfileController::class, 'changePassword'])->name('profile.ganti-password');
    Route::put('profile/update-profile/{id}', [ProfileController::class, 'updateProfile'])->name('profile.update-profile');

    // only administrator can acces this route
    Route::middleware('role:administrator')->group(function () {
        // User
        Route::match(['get', 'post'], 'user', [UserController::class, 'index'])->name('user.index');
        Route::get('user/tambah', [UserController::class, 'create'])->name('user.create');
        Route::post('user/store', [UserController::class, 'store'])->name('user.store');
        Route::get('user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::put('user/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
        
        // Master lantai
        Route::match(['get', 'post'], 'lantai', [FloorController::class, 'index'])->name('floor.index');
        Route::get('lantai/tambah', [FloorController::class, 'create'])->name('floor.create');
        Route::post('lantai/store', [FloorController::class, 'store'])->name('floor.store');
        Route::get('lantai/edit/{id}', [FloorController::class, 'edit'])->name('floor.edit');
        Route::put('lantai/{id}', [FloorController::class, 'update'])->name('floor.update');
        Route::delete('lantai/{id}', [FloorController::class, 'destroy'])->name('floor.destroy');
    
        // Master tipe kamar
        Route::match(['get', 'post'], 'tipe-kamar', [RoomTypeController::class, 'index'])->name('room-types.index');
        Route::get('tipe-kamar/tambah', [RoomTypeController::class, 'create'])->name('room-types.create');
        Route::post('tipe-kamar/store', [RoomTypeController::class, 'store'])->name('room-types.store');
        Route::get('tipe-kamar/edit/{id}', [RoomTypeController::class, 'edit'])->name('room-types.edit');
        Route::put('tipe-kamar/{id}', [RoomTypeController::class, 'update'])->name('room-types.update');
        Route::delete('tipe-kamar/{id}', [RoomTypeController::class, 'destroy'])->name('room-types.destroy');
    
        // Master Kamar
        Route::match(['get', 'post'], 'kamar', [RoomController::class, 'index'])->name('room.index');
        Route::get('kamar/tambah', [RoomController::class, 'create'])->name('room.create');
        Route::post('kamar/store', [RoomController::class, 'store'])->name('room.store');
        Route::get('kamar/edit/{id}', [RoomController::class, 'edit'])->name('room.edit');
        Route::put('kamar/{id}', [RoomController::class, 'update'])->name('room.update');
        Route::delete('kamar/{id}', [RoomController::class, 'destroy'])->name('room.destroy');
    });
    
    Route::get('lantai/search', [FloorController::class, 'search'])->name('floor.search');
    Route::get('tipe-kamar/search', [RoomTypeController::class, 'search'])->name('room-types.search');
    Route::get('kamar/search', [RoomController::class, 'search'])->name('room.search');
    Route::get('kamar/json/{id}', [RoomController::class, 'getJson'])->name('room.getJson');

    // Master buku tamu
    Route::match(['get', 'post'], 'tamu', [GuestController::class, 'index'])->name('guest.index');
    Route::get('tamu/tambah', [GuestController::class, 'create'])->name('guest.create');
    Route::post('tamu/store', [GuestController::class, 'store'])->name('guest.store');
    Route::get('tamu/edit/{id}', [GuestController::class, 'edit'])->name('guest.edit');
    Route::put('tamu/{id}', [GuestController::class, 'update'])->name('guest.update');
    Route::get('tamu/search', [GuestController::class, 'search'])->name('guest.search');

    // Reservasi
    Route::match(['get', 'post'], 'reservasi', [ReservationController::class, 'index'])->name('reservation.index');
    Route::get('reservasi/tambah', [ReservationController::class, 'create'])->name('reservation.create');
    Route::post('reservasi/store', [ReservationController::class, 'store'])->name('reservation.store');
    Route::get('reservasi/edit/{id}', [ReservationController::class, 'edit'])->name('reservation.edit');
    Route::get('reservasi/show/{id}', [ReservationController::class, 'show'])->name('reservation.show');
    Route::put('reservasi/{id}', [ReservationController::class, 'update'])->name('reservation.update');
    Route::put('reservasi/confirm/{id}', [ReservationController::class, 'confirm'])->name('reservation.confirm');
    Route::put('reservasi/check-out/{id}', [ReservationController::class, 'checkOut'])->name('reservation.checkOut');
    Route::put('reservasi/cancel/{id}', [ReservationController::class, 'cancel'])->name('reservation.cancel');
    
    // Checking lansung
    Route::get('reservasi/check-in/{id}', [CheckinController::class, 'checkIn'])->name('reservation.checkIn');
    Route::put('reservasi/check-in/{id}', [CheckinController::class, 'checkInProcess'])->name('reservation.checkInProcess');
    Route::get('reservasi/checkin-langsung', [CheckinController::class, 'createDirectCheckin'])->name('reservation.direct.create');
    Route::post('reservasi/checkin-langsung', [CheckinController::class, 'storeDirectCheckin'])->name('reservation.direct.store');
    
    // Pembayaran
    Route::get('reservasi/pembayaran/tambah/{reservation_id}', [PaymentController::class, 'create'])->name('reservation.payment.create');
    Route::post('reservasi/pembayaran/store/{reservation_id}', [PaymentController::class, 'store'])->name('reservation.payment.store');

    // Invoice
    Route::get('reservasi/invoice/{id}', [PrintController::class, 'invoice'])->name('reservation.invoice');

    // Laporan Reservasi
    Route::match(['get', 'post'], 'laporan-reservasi', [ReportReservationController::class, 'index'])->name('report-reservation.index');
    Route::get('laporan-reservasi/export/excel', [ReportReservationController::class, 'exportExcel'])->name('report-reservation.export.excel');
    Route::get('laporan-reservasi/export/pdf', [ReportReservationController::class, 'exportPDF'])->name('report-reservation.export.pdf');

    // Laporan Pembayaran
    Route::match(['get', 'post'], 'laporan-pembayaran', [ReportPaymentController::class, 'index'])->name('report-payment.index');
    Route::get('laporan-pembayaran/export/excel', [ReportPaymentController::class, 'exportExcel'])->name('report-payment.export.excel');
    Route::get('laporan-pembayaran/export/pdf', [ReportPaymentController::class, 'exportPDF'])->name('report-payment.export.pdf');
});
