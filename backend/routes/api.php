<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::prefix('task')->group(function () {
    Route::post('/create', [TaskController::class, 'create']);
    Route::get('/list', [TaskController::class, 'list']);
    Route::post('/complete/{id}', [TaskController::class, 'complete']);
});