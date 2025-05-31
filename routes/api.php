<?php

use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpeakersController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/speakers', [App\Http\Controllers\SpeakersController::class, 'index']);
Route::get('/all/speakers', [App\Http\Controllers\SpeakersController::class, 'allSpeakers']);
Route::get('/speakers/{id}', [App\Http\Controllers\SpeakersController::class, 'show']);
Route::get('/speakers-by-year', [App\Http\Controllers\SpeakersController::class, 'speakersByYear']);
Route::get('/top/speakers', [App\Http\Controllers\SpeakersController::class, 'getTopSpeakers']);
Route::get('/images/{filename}', [ImageController::class, 'show']);

//programs
Route::get('/programs', [App\Http\Controllers\ProgramsController::class, 'index']);
Route::get('/programs/{id}', [App\Http\Controllers\ProgramsController::class, 'show']);
Route::get('/programs/current/year', [App\Http\Controllers\ProgramsController::class, 'currentYearProgram']);

//news
Route::get('/news', [App\Http\Controllers\NewsController::class, 'index']);
Route::get('/news/event', [App\Http\Controllers\NewsController::class, 'getByEventTime']);

Route::get('/all/news', [App\Http\Controllers\NewsController::class, 'latestNews']);
Route::get('/all-news', [App\Http\Controllers\NewsController::class, 'newsWithFilter']);


Route::get('/news/{id}', [App\Http\Controllers\NewsController::class, 'show']);


//stream
Route::get('/stream', [App\Http\Controllers\StreamController::class, 'index']);
Route::get('/stream/{id}', [App\Http\Controllers\StreamController::class, 'show']);

// Conference routes
Route::get('/conferences', [ConferenceController::class, 'index']);
Route::get('/conferences/last', [ConferenceController::class, 'last']);


//orgs
Route::get('/orgs', [App\Http\Controllers\OrganizersController::class, 'index']);

//Partners
Route::get('/partners', [App\Http\Controllers\PartnersController::class, 'index']);
