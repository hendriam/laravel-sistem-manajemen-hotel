<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\RoomController;

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

    // Master Kamar
    Route::match(['get', 'post'], 'kamar', [RoomController::class, 'index'])->name('room.index');
    Route::get('kamar/tambah', [RoomController::class, 'create'])->name('room.create');
    Route::post('kamar/store', [RoomController::class, 'store'])->name('room.store');
    Route::get('kamar/edit/{id}', [RoomController::class, 'edit'])->name('room.edit');
    Route::put('kamar/{id}', [RoomController::class, 'update'])->name('room.update');
    Route::delete('kamar/{id}', [RoomController::class, 'destroy'])->name('room.destroy');
});
