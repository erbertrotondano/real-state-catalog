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
	
	Route::post('/login', 'Auth\\LoginJwtController@login')->name('login');
	Route::get('/logout', 'Auth\\LoginJwtController@logout')->name('logout');
	Route::get('/refresh', 'Auth\\LoginJwtController@refresh')->name('refresh');

	Route::group(['middleware' => ['jwt.auth']], function() {
		Route::name('real_states.')->group(function(){
			Route::resource('real-states', 'RealStateController');
		});
		Route::name('users.')->group(function(){
			Route::resource('users', 'UserController');
		});
		Route::name('categories.')->group(function(){
			Route::resource('categories', 'CategoryController');
			Route::get('categories/{id}/real-states', 'CategoryController@realStates');
		});
		Route::name('photos.')->prefix('photos')->group(function() {
			Route::delete('/{id}', 'RealStatePhotoController@remove')->name('delete');
			Route::put('/set-thumb/{photoId}/{realStateId}', 'RealStatePhotoController@setThumb')->name('setThumb');
		});
	});
	
});

