<?php

use App\Models\CalculatorItem;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'prefix'     => 'v1',
    'middleware' => ['api'],
    'namespace'  => 'App\Http\Controllers\Api\V1',
], function () { // custom admin routes
    Route::get('users', 'UserController@index');
    Route::get('cases', 'CaseController@index');
    Route::get('time-slot', 'CaseController@getTimeSlot')->name('api.time-slot');
    Route::post('get-latest-case-detail-status-log', 'CaseController@getLatestCaseDetailStatusLog')->name('api.get-latest-case-detail-status-log');
    Route::put('calculator/{cid}', 'CalculatorController@update');
});

