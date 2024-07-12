<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/not-authorized', function () {
    return $this->responseFail('Unauthorized, Please Login.',401);
})->name('login');
Route::post('/login', [UserController::class, 'LoginProcessing']);
Route::get('/client', [UserController::class, 'Show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/all-template', [UserController::class, 'GetAllTemplate']);
    Route::get('/templates/{template}', [UserController::class, 'GetTemplate']);
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/templates', [UserController::class, 'AddTemplate']);
        Route::post('/templates/{template}', [UserController::class, 'CloneTemplate']);
        Route::post('/{template}/sections', [UserController::class, 'AddSection']);
        Route::put('/templates/{template}', [UserController::class, 'EditTemplate']);
        Route::delete('/templates', [UserController::class, 'DeleteTemplate']);
        Route::delete('/sections/{section}', [UserController::class, 'DeleteSection']);
        Route::put('/show/{template}', [UserController::class, 'ChangeTemplate']);
        Route::put('/{template}/sections/{section}', [UserController::class, 'EditSection']);
        Route::put('/templates/{templateId}/header', [UserController::class, 'EditHeader']);
        Route::put('/templates/{templateId}/footer', [UserController::class, 'EditFooter']);
        Route::post('/{template}/ava', [UserController::class, 'EditAvatar']);
    });
});
