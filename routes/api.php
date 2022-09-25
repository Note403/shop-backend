<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::group(['prefix' => '/user'], function () {
    Route::get('', [UserController::class, 'all'])
        ->name('user.all');

    Route::post('/', [UserController::class, 'create'])
        ->name('user.create');

    Route::delete('/{user_id}', [UserController::class, 'delete'])
        ->name('user.delete');

    Route::get('/{user_id}', [UserController::class, 'getById'])
        ->whereUuid('user_id')
        ->name('user.byId');

    Route::get('role/{role}', [UserController::class, 'getByRole'])
        ->name('user.byRole');

    Route::get('/blocked', [UserController::class, 'getBlocked'])
        ->name('user.getBlocked');

    Route::get('/in_time/{start_date}/{end_date}', [UserController::class, 'getCreatedInTime'])
        ->name('user.createdTime');
});
