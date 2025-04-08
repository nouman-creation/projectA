<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageMoveController;
use App\Http\Controllers\TimeZoneController;

// Time Zone Converter Routes
Route::get('/', [TimeZoneController::class, 'index'])->name('timezone.index');
Route::post('/timezone/get-time', [TimeZoneController::class, 'getTime'])->name('timezone.getTime');
Route::get('/timezone/search-cities', [TimeZoneController::class, 'searchCities'])->name('timezone.searchCities');

// Image Move Route
Route::get('/move', [ImageMoveController::class, 'moveImages']);