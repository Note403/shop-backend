<?php

use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\ArticleController;
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
    Route::get('/', [UserController::class, 'all'])
        ->name('user.all');

    Route::post('/', [UserController::class, 'create'])
        ->name('user.create');

    Route::delete('/{user_id}', [UserController::class, 'delete'])
        ->name('user.delete');

    Route::get('/{user_id}', [UserController::class, 'getById'])
        ->whereUuid('user_id')
        ->name('user.byId');

    Route::post('/change/request/address', [UserController::class, 'requestAddressChange'])
        ->name('user.request_address_change');

    Route::post('/change/request/pw', [UserController::class, 'requestPwChange'])
        ->name('user.request_pw_change');

    Route::post('/change/address', [UserController::class, 'processAddrChange'])
        ->name('user.process_address_change');

    Route::post('/change/pw', [UserController::class, 'processPwChange'])
        ->name('user.process_pw_change');

    Route::get('role/{role}', [UserController::class, 'getByRole'])
        ->name('user.byRole');

    Route::get('/blocked', [UserController::class, 'getBlocked'])
        ->name('user.getBlocked');

    Route::get('/in_time/{start_date}/{end_date}', [UserController::class, 'getCreatedInTime'])
        ->name('user.createdTime');

    Route::post('/login', [UserController::class, 'login'])
        ->name('user.login');

    Route::post('/logout', [UserController::class, 'logout'])
        ->name('user.logout');

    Route::get('/me', [UserController::class, 'me'])
        ->name('user.me');
});

Route::group(['prefix' => '/ac'], function () {
    Route::get('/' , [ArticleCategoryController::class, 'all'])
        ->name('ac.all');

    Route::get('/name/{name}', [ArticleCategoryController::class, 'byName'])
        ->name('ac.name');

    Route::get('/key/{key}', [ArticleCategoryController::class, 'byKey'])
        ->name('ac.key');

    Route::post('/', [ArticleCategoryController::class, 'create'])
        ->name('ac.create');

    Route::patch('/{ac_id}', [ArticleCategoryController::class, 'patch'])
        ->name('ac.patch');

    Route::delete('/{ac_id}', [ArticleCategoryController::class, 'delete'])
        ->name('ac.delete');
});


Route::group(['prefix' => '/article'], function () {
    Route::get('/', [ArticleController::class, 'all'])
        ->name('article.all');

    Route::get('/{article_id}', [ArticleController::class, 'byId'])
        ->name('article.id');

    Route::get('/category/{category_key}', [ArticleController::class, 'byCategory'])
        ->name('article.category');

    Route::get('/price_range/{min_price}/{max_price}', [ArticleController::class, 'inPriceRange'])
        ->name('article.price_range');

    Route::get('/title/{title}', [ArticleController::class, 'byTitle'])
        ->name('article.title');

    Route::post('/', [ArticleController::class, 'create'])
        ->name('article.create');

    Route::patch('/{article_id}', [ArticleController::class, 'patch'])
        ->name('article.patch');

    Route::delete('/{article_id}', [ArticleController::class, 'delete'])
        ->name('article.delete');
});
