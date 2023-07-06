<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group([
  'middleware' => 'api',
  'prefix' => 'auth',
], function () {
  Route::post('register', [AuthController::class, 'register']);
  Route::post('login', [AuthController::class, 'login']);
  Route::post('restore', [AuthController::class, 'restore']);

  Route::post('logout', [AuthController::class, 'logout']);
  Route::post('refresh', [AuthController::class, 'refresh']);
  Route::get('me', [AuthController::class, 'me']);
});
