<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/not-authorized', function () {
    return $this->responseFail('Unauthorized, Please Login.',401);
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
        Route::post('/{template}/section', [UserController::class, 'AddSection']);
        Route::put('/template/{template}', [UserController::class, 'EditTemplate']);
        Route::delete('/template', [UserController::class, 'DeleteTemplate']);
        Route::delete('/section/{section}', [UserController::class, 'DeleteSection']);
        Route::put('/show/{template}', [UserController::class, 'ChangeTemplate']);
        Route::put('/{template}/section/{section}', [UserController::class, 'EditSection']);
        Route::put('/template/{templateId}/header', [UserController::class, 'EditHeader']);
        Route::put('/template/{templateId}/footer', [UserController::class, 'EditFooter']);
        Route::post('/{template}/ava', [UserController::class, 'EditAvatar']);
    });
});

// DELETE THE ROUTE BELOW WHEN COMPLETED
Route::get('/template/{template}', [UserController::class, 'GetTemplate']);
Route::post('/{template}/ava', [UserController::class, 'EditAvatar']);
Route::get('/allTemplate', function () {
    $show = $this->showRepository->getShow();
        return $this->responseSuccess([
            'username' => '$user->username',
            'chosen' => $show->template_id,
            'templates' => $this->templateRepository->getAllTemplate(),
        ]);
});
