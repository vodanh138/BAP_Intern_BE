<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/not-authorized', function () {
    return response()->json(['error' => 'Unauthorized. Please login.'], 401);
})->name('login');
Route::post('/LoginProcessing', [UserController::class, 'LoginProcessing']);
Route::get('/', [UserController::class, 'Show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/GetAllTemplate', [UserController::class, 'GetAllTemplate']);
    Route::get('/GetTemplate/{template}', [UserController::class, 'GetTemplate']);
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/AddTemplate', [UserController::class, 'AddTemplate']);
        Route::post('/CloneTemplate/{template}', [UserController::class, 'CloneTemplate']);
        Route::post('/AddSecion', [UserController::class, 'AddSecion']);
        Route::put('/EditTemplate/{template}', [UserController::class, 'EditTemplate']);
        Route::delete('/DeleteTemplate/{template}', [UserController::class, 'DeleteTemplate']);
        Route::delete('/DeleteSecion/{section}', [UserController::class, 'DeleteSecion']);
        Route::put('/{template}', [UserController::class, 'ChangeTemplate']);
        Route::put('/EditSection/{section}', [UserController::class, 'EditSection']);
    });
});
