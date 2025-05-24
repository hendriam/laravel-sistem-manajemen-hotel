<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ReservationController;

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/', [AuthController::class, 'authenticate']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master lantai
    Route::match(['get', 'post'], 'lantai', [FloorController::class, 'index'])->name('floor.index');
    Route::get('lantai/tambah', [FloorController::class, 'create'])->name('floor.create');
    Route::post('lantai/store', [FloorController::class, 'store'])->name('floor.store');
    Route::get('lantai/edit/{id}', [FloorController::class, 'edit'])->name('floor.edit');
    Route::put('lantai/{id}', [FloorController::class, 'update'])->name('floor.update');
    Route::delete('lantai/{id}', [FloorController::class, 'destroy'])->name('floor.destroy');
    Route::get('buku/search', [FloorController::class, 'search'])->name('floor.search');

    // Master tipe kamar
    Route::match(['get', 'post'], 'tipe-kamar', [RoomTypeController::class, 'index'])->name('room-types.index');
    Route::get('tipe-kamar/tambah', [RoomTypeController::class, 'create'])->name('room-types.create');
    Route::post('tipe-kamar/store', [RoomTypeController::class, 'store'])->name('room-types.store');
    Route::get('tipe-kamar/edit/{id}', [RoomTypeController::class, 'edit'])->name('room-types.edit');
    Route::put('tipe-kamar/{id}', [RoomTypeController::class, 'update'])->name('room-types.update');
    Route::delete('tipe-kamar/{id}', [RoomTypeController::class, 'destroy'])->name('room-types.destroy');
    Route::get('tipe-kamar/search', [RoomTypeController::class, 'search'])->name('room-types.search');

    // Master Kamar
    Route::match(['get', 'post'], 'kamar', [RoomController::class, 'index'])->name('room.index');
    Route::get('kamar/tambah', [RoomController::class, 'create'])->name('room.create');
    Route::post('kamar/store', [RoomController::class, 'store'])->name('room.store');
    Route::get('kamar/edit/{id}', [RoomController::class, 'edit'])->name('room.edit');
    Route::put('kamar/{id}', [RoomController::class, 'update'])->name('room.update');
    Route::delete('kamar/{id}', [RoomController::class, 'destroy'])->name('room.destroy');
    Route::get('kamar/search', [RoomController::class, 'search'])->name('room.search');

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
    Route::put('reservasi/{id}', [ReservationController::class, 'update'])->name('reservation.update');
    Route::put('reservasi/check-in/{id}', [ReservationController::class, 'checkIn'])->name('reservation.checkIn');
    Route::put('reservasi/check-out/{id}', [ReservationController::class, 'checkOut'])->name('reservation.checkOut');
    Route::put('reservasi/cancel/{id}', [ReservationController::class, 'cancel'])->name('reservation.cancel');
    Route::get('reservasi/checkin-langsung', [ReservationController::class, 'createDirectCheckin'])->name('reservation.direct.create');
    Route::post('reservasi/checkin-langsung', [ReservationController::class, 'storeDirectCheckin'])->name('reservation.direct.store');
});
