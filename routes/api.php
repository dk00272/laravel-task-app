<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;

Route::prefix('v1')->group(function () {

    Route::post('/register', [
        AuthController::class,
        'register'
    ]);

    Route::post('/login', [
        AuthController::class,
        'login'
    ]);

    Route::middleware('auth:sanctum')
        ->group(function () {

            Route::post('/logout', [
                AuthController::class,
                'logout'
            ]);

            Route::apiResource(
                'tasks',
                TaskController::class
            );
        });

   Route::middleware('auth:sanctum')->group(function () {

   Route::get('summary', [TaskController::class, 'summary']);


});

});