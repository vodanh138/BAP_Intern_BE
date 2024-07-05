<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/not-authorized', function () {
    return response()->json([
        'status' => 'fail',
        'message' => 'Unauthorized, Please Login.'
    ], 401);
})->name('login');
Route::post('/LoginProcessing', [UserController::class, 'LoginProcessing']);
Route::get('/ClientView', [UserController::class, 'Show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/GetAllTemplate', [UserController::class, 'GetAllTemplate']);
    Route::get('/GetTemplate/{template}', [UserController::class, 'GetTemplate']);
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/AddTemplate', [UserController::class, 'AddTemplate']);
        Route::post('/CloneTemplate/{template}', [UserController::class, 'CloneTemplate']);
        Route::post('/AddSection', [UserController::class, 'AddSection']);
        Route::put('/EditTemplate/{template}', [UserController::class, 'EditTemplate']);
        Route::delete('/DeleteTemplate', [UserController::class, 'DeleteTemplate']);
        Route::delete('/DeleteSection/{section}', [UserController::class, 'DeleteSection']);
        Route::put('/ChooseTemplate/{template}', [UserController::class, 'ChangeTemplate']);
        Route::put('/EditSection/{section}', [UserController::class, 'EditSection']);
    });
});
