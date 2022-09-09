<?php

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

Route::get('auth/request-challenge/{address}', 'App\Http\Controllers\AuthController@requestChallenge');
Route::post('auth/login', 'App\Http\Controllers\AuthController@login');

Route::middleware('auth:api')->group(static function () {
    Route::get('whoami', 'App\Http\Controllers\AuthController@whoami');

    Route::get('pool-metas', 'App\Http\Controllers\PoolMetaController@index');

    Route::post('nfts/sync', 'App\Http\Controllers\NftController@sync');
    Route::post('nfts/{assetId}/cache-image', 'App\Http\Controllers\NftController@cacheImage');
    Route::post('nfts/add-to-pool', 'App\Http\Controllers\NftController@addToPool');
});
