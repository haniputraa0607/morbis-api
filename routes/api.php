<?php

use App\Http\Controllers\AccessTokenController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
Route::controller(AccessTokenController::class)->prefix('/login')->group(function () {
    Route::post('/', 'login')->name('login');
});

Route::controller(AccessTokenController::class)->prefix('/logout')->group(function () {
    Route::post('/', 'logout')->name('login');
});

Route::middleware(['auth:api','scopes:admin'])->controller(AdminController::class)->prefix('admin')->group(function (){
    Route::get('list-queue', 'listQueue')->name('listQueue');
    Route::get('queue/{status?}', 'detailQueue')->name('detailQueue');
    Route::get('finished', 'finishedQueue')->name('finishedQueue');
});

Route::controller(UserController::class)->prefix('user')->group(function (){
    Route::get('create-queue', 'createQueue')->name('createQueue');
});
