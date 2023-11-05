<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('hello', function() {
	return ['msg' => 'Its aliiiive'];
});

Route::prefix('v1')->namespace('App\Http\Controllers\Api')->group(function() {
	Route::name('real_states.')->group(function(){
		
		Route::resource('real-states', 'RealStateController');

	});
});

