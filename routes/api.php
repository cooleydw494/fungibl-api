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
Route::get('pool-metas', 'App\Http\Controllers\PoolMetaController@index');
Route::get('pool-metas/logs', 'App\Http\Controllers\PoolMetaController@getLogs');
Route::get('pool-metas/latest-logs', 'App\Http\Controllers\PoolMetaController@getLatestLogs');
Route::post('process-contact-form', 'App\Http\Controllers\MarketingController@processContactForm');

Route::middleware('auth:api')->group(static function () {
    Route::get('whoami', 'App\Http\Controllers\AuthController@whoami');
    Route::post('update-nfd-info', 'App\Http\Controllers\UserController@updateNfdInfo');

    Route::post('nfts/sync', 'App\Http\Controllers\NftController@sync');
    Route::post('nfts/{assetId}/cache-image', 'App\Http\Controllers\NftController@cacheImage');
    Route::post('nfts/add-to-pool', 'App\Http\Controllers\NftController@addToPool');
    Route::post('create-submit-contract', 'App\Http\Controllers\NftController@createSubmitContract');
    Route::post('set-puller', 'App\Http\Controllers\NftController@setPuller');
    Route::post('random-contract-info', 'App\Http\Controllers\NftController@randomContractInfo');
    Route::get('featured-nft-info', 'App\Http\Controllers\NftController@featuredNftInfo');
//    Route::post('nfts/{assetId}/pulled', 'App\Http\Controllers\NftController@markPulled');

    Route::get('estimate/{nft}', 'App\Http\Controllers\NftController@getEstimate');
});
