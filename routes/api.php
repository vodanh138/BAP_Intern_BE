<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/not-authorized', function () {
    return response()->json([
        'status' => 'fail',
        'message' => 'Unauthorized, Please Login.'
    ], 401);
})->name('login');
Route::post('/loginProcessing', [UserController::class, 'LoginProcessing']);
Route::get('/clientView', [UserController::class, 'Show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/allTemplate', [UserController::class, 'GetAllTemplate']);
    Route::get('/template/{template}', [UserController::class, 'GetTemplate']);
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/template', [UserController::class, 'AddTemplate']);
        Route::post('/template/{template}', [UserController::class, 'CloneTemplate']);
        Route::post('/section', [UserController::class, 'AddSection']);
        Route::put('/template/{template}', [UserController::class, 'EditTemplate']);
        Route::delete('/template', [UserController::class, 'DeleteTemplate']);
        Route::delete('/section/{section}', [UserController::class, 'DeleteSection']);
        Route::put('/show/{template}', [UserController::class, 'ChangeTemplate']);
        Route::put('/section/{section}', [UserController::class, 'EditSection']);

        Route::put('/template/{templateId}/header', [UserController::class, 'EditHeader']);
        Route::put('/template/{templateId}/footer', [UserController::class, 'EditFooter']);
    });
});
