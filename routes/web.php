<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageMoveController;

Route::get('/', function () {
    return view('welcome');
});


// Route::get('/move','ImageMoveController@moveImages');
Route::get('/move',[ImageMoveController::class,'moveImages']);