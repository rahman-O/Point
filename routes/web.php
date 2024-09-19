<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::view('/{any}', 'app')->where('any', '.*');

