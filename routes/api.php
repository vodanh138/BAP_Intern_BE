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
    Route::get('/AllTemplate', [UserController::class, 'GetAllTemplate']);
    Route::get('/Template/{template}', [UserController::class, 'GetTemplate']);
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/Template', [UserController::class, 'AddTemplate']);
        Route::post('/Template/{template}', [UserController::class, 'CloneTemplate']);
        Route::post('/Section', [UserController::class, 'AddSection']);
        Route::put('/Template/{template}', [UserController::class, 'EditTemplate']);
        Route::delete('/Template', [UserController::class, 'DeleteTemplate']);
        Route::delete('/Section/{section}', [UserController::class, 'DeleteSection']);
        Route::put('/Show/{template}', [UserController::class, 'ChangeTemplate']);
        Route::put('/Section/{section}', [UserController::class, 'EditSection']);
    });
});
