<?php

use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

Route::post('/imports', [ImportController::class, 'store']);
Route::get('/imports', [ImportController::class, 'index']);
Route::get('/imports/{id}', [ImportController::class, 'show']);
