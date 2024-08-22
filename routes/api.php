<?php

use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/speakers', [App\Http\Controllers\SpeakersController::class, 'index']);
Route::get('/images/{filename}', [ImageController::class, 'show']);
