<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {

    /**
     * Auth Routes
     *
     * These routes handle user authentication, including login, registration, and logout.
    */
    Route::controller(AuthController::class)->group(function () {
        /**
         * Login Route
         *
         * @method POST
         * @route /v1/login
         * @desc Authenticates a user and returns a JWT token.
         */
        Route::post('login', 'login');

        /**
         * Register Route
         *
         * @method POST
         * @route /v1/register
         * @desc Registers a new user and returns a JWT token.
         */
        Route::post('register', 'register');

        /**
         * Logout Route
         *
         * @method POST
         * @route /v1/logout
         * @desc Logs out the authenticated user.
         * @middleware auth:api
         */
        Route::post('logout', 'logout')->middleware('auth:api');
    });

    // resource for Task CRUD
    Route::apiResource('tasks', TaskController::class)->middleware(['auth:api']);
    // route for assigne task to user
    Route::post('tasks/{id}/assign',[TaskController::class,'assigne'])->middleware('auth:api');
    //route to change status of task
    Route::post('tasks/{id}/status',[TaskController::class,'editTaskStatus'])->middleware('auth:api');
    //route for user CRUD
    Route::apiResource('users',UserController::class)->middleware('auth:api');
    // for restore user
    Route::post('restore-user/{id}',[UserController::class,'retrieve'])->middleware('auth:api');
    // for restore task
    Route::post('restore-task/{id}',[TaskController::class,'retrieve'])->middleware('auth:api');

});
