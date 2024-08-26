<?php

use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/speakers', [App\Http\Controllers\SpeakersController::class, 'index']);
Route::get('/images/{filename}', [ImageController::class, 'show']);

//programs
Route::get('/programs', [App\Http\Controllers\ProgramsController::class, 'index']);
Route::get('/programs/{id}', [App\Http\Controllers\ProgramsController::class, 'show']);


//news
Route::get('/news', [App\Http\Controllers\NewsController::class, 'index']);
Route::get('/news/{id}', [App\Http\Controllers\NewsController::class, 'show']);


//stream
Route::get('/stream', [App\Http\Controllers\StreamController::class, 'index']);
Route::get('/stream/{id}', [App\Http\Controllers\StreamController::class, 'show']);

// Conference routes
Route::get('/conferences', [ConferenceController::class, 'index']);
Route::get('/conferences/last', [ConferenceController::class, 'last']);
