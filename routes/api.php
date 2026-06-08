<?php

use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\ProgramsController;
use App\Http\Controllers\SpeakersController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\OrganizersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/speakers', [SpeakersController::class, 'index']);
Route::get('/all/speakers', [SpeakersController::class, 'allSpeakers']);
Route::get('/speakers/{id}', [SpeakersController::class, 'show']);
Route::get('/speakers-by-year', [SpeakersController::class, 'speakersByYear']);
Route::get('/top/speakers', [SpeakersController::class, 'getTopSpeakers']);
Route::get('/images/{filename}', [ImageController::class, 'show']);

//programs
Route::get('/programs', [ProgramsController::class, 'index']);
Route::get('/programs/{id}', [ProgramsController::class, 'show']);
Route::get('/programs/current/year', [ProgramsController::class, 'currentYearProgram']);

//news
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/event', [NewsController::class, 'getByEventTime']);

Route::get('/all/news', [NewsController::class, 'latestNews']);
Route::get('/all-news', [NewsController::class, 'newsWithFilter']);

Route::post('/news', [NewsController::class, 'store']);
Route::get('/news/{id}', [NewsController::class, 'show']);


//stream
Route::get('/stream', [StreamController::class, 'index']);
Route::get('/stream/{id}', [StreamController::class, 'show']);

// Conference routes
Route::get('/conferences', [ConferenceController::class, 'index']);
Route::get('/conferences/last', [ConferenceController::class, 'last']);


//orgs
Route::get('/orgs', [OrganizersController::class, 'index']);

//Partners
Route::get('/partners', [PartnersController::class, 'index']);
