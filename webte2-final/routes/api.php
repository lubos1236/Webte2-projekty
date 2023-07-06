<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

require __DIR__ . '/auth.php';

Route::group(['prefix' => 'students', 'middleware' => 'auth'], function () {
  Route::get('/', [StudentController::class, 'index']);
  Route::get('/{id}', [StudentController::class, 'show']);
});

Route::group(['prefix' => 'users', 'middleware' => 'auth'], function () {
  Route::get('/', [UserController::class, 'index']);
  Route::delete('/', [UserController::class, 'deleteUser']);
  Route::put('/', [UserController::class, 'changeRole']);
});

Route::group(['prefix' => 'exercises', 'middleware' => 'auth'], function () {
  Route::get('/', [ExerciseController::class, 'index']);
  Route::get('/{exercise_set}', [ExerciseController::class, 'show']);
});

Route::group(['prefix' => 'assignment-groups', 'middleware' => 'auth'], function () {
  Route::post('/', [AssignmentController::class, 'create']);
  Route::get('/', [AssignmentController::class, 'index']);
  Route::get('/{id}', [AssignmentController::class, 'show']);
});

Route::group(['prefix' => 'submissions', 'middleware' => 'auth'], function () {
  Route::post('/{submission}', [SubmissionController::class, 'generate']);
  Route::post('/{submission}/submit', [SubmissionController::class, 'submit']);
});

Route::group(['prefix' => 'docs', 'middleware' => 'auth'], function () {
  Route::get('/{role}', [DocsController::class, 'show']);
});
