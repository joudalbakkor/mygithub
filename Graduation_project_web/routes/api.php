<?php

use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\categoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
* auth for users(admin , super admin)
*/
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

/*
* auth for seller
*/
Route::group(['prefix' => 'seller', 'as' => 'seller.'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/seller-profile', [AuthController::class, 'userProfile']);
    
});


/**
 * middleware
 */

Route::group(['prefix' => 'superadmin','middleware' => ['jwt.verify:api','jwt.auth']],function ()
{
	
});

Route::group(['prefix' => 'sellers','middleware' => ['jwt.verify:api','jwt.auth']],function ()
{

});

Route::group(['prefix' => 'admin','middleware' => ['jwt.verify:api','jwt.auth']],function ()
{
	Route::post('createcategory', [categoryController::class, 'store']);
    Route::put('updatecategory/{id}', [categoryController::class, 'update']);
    Route::delete('deletecategory/{id}', [categoryController::class, 'destroy']);

    
    Route::post('createstore', [StoreController::class, 'store']);
    Route::put('updatestore/{id}', [StoreController::class, 'update']);
    Route::delete('deletestore/{id}', [StoreController::class, 'destroy']);
});


/**
 * Routes none middleware for public
 */

Route::get('categories', [categoryController::class, 'index']);

Route::get('/categories/{category}/stores', [categoryController::class, 'stores']);

Route::get('showecategory/{id}', [categoryController::class, 'show']);


Route::get('stores', [StoreController::class, 'index']);
Route::get('detialstore/{id}', [StoreController::class, 'show']);




