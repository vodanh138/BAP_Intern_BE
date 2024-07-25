<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/not-authorized', function () {
    return $this->responseFail(__('messages.unauthor'), 401);
})->name('login');
Route::post('/login', [UserController::class, 'loginProcessing']);
Route::get('/client', [UserController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/all-template', [UserController::class, 'getAllTemplate']);
    Route::get('/templates/{template}', [UserController::class, 'getTemplate']);
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/templates', [UserController::class, 'addTemplate']);
        Route::post('/templates/{template}', [UserController::class, 'cloneTemplate']);
        Route::post('/{template}/sections', [UserController::class, 'addSection']);
        Route::delete('/templates', [UserController::class, 'deleteTemplate']);
        Route::delete('/sections/{section}', [UserController::class, 'deleteSection']);
        Route::put('/show/{template}', [UserController::class, 'changeTemplate']);
        Route::put('/{template}/sections/{section}', [UserController::class, 'editSection']);
        Route::put('/templates/{templateId}/header', [UserController::class, 'editHeader']);
        Route::put('/templates/{templateId}/footer', [UserController::class, 'editFooter']);
        Route::post('/{template}/ava', [UserController::class, 'editAvatar']);
    });
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Route::post('/{locale}/login', [UserController::class, 'loginProcessingLocale']);
//Route::get('/templates/{template}', [UserController::class, 'getTemplate']);
