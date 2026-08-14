<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\PromotionMediaController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authenticate']);

Route::middleware(['auth'])->group(
    function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::resource('location', LocationController::class);
        Route::get('/locations/data', [LocationController::class, 'data']);

        Route::resource('display', DisplayController::class);
        Route::get('/displays/data', [DisplayController::class, 'data']);

        Route::resource('event', EventController::class);
        Route::get('/events/data', [EventController::class, 'data']);

        Route::resource('promotion', PromotionController::class);
        Route::get('/promotions/data', [PromotionController::class, 'data']);

        Route::resource('schedule', ScheduleController::class);
        Route::get('/schedules/data', [ScheduleController::class, 'data']);

        Route::get('design/{id}', [DesignController::class, 'index'])->name('design.index');
        Route::get('design/create/{id}', [DesignController::class, 'create'])->name('design.create');
        Route::post('design/store/{id}', [DesignController::class, 'store'])->name('design.store');
        Route::delete('design/destroy/{id}/{designId}', [DesignController::class, 'destroy'])->name('design.destroy');
        Route::get('design/edit/{id}/{designId}', [DesignController::class, 'edit'])->name('design.edit');
        Route::put('design/edit/{id}/{designId}', [DesignController::class, 'update'])->name('design.update');
        Route::get('/designs/data/{id}', [DesignController::class, 'data']);

        Route::get('/promotion-media/{id}', [PromotionMediaController::class, 'index'])->name('promotion-media.index');
        Route::get('/promotion-media/create/{id}', [PromotionMediaController::class, 'create'])->name('promotion-media.create');
        Route::post('/promotion-media/store/{id}', [PromotionMediaController::class, 'store'])->name('promotion-media.store');
        Route::delete('/promotion-media/destroy/{id}/{mediaId}', [PromotionMediaController::class, 'destroy'])->name('promotion-media.destroy');
        Route::get('/promotion-medias/data/{id}', [PromotionMediaController::class, 'data']);

        Route::resource('admin', AdminController::class);
        Route::get('/admins/data', [AdminController::class, 'data']);
    }
);
