<?php

use Illuminate\Http\Request;

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
Route::get('user/check/{username}', ['as' => 'user.check',
                                    'uses' => 'UserController@check']);
Route::get('user/checkEmail/{email}', ['as' => 'user.checkEmail',
                                      'uses' => 'UserController@checkEmail']);
Route::get('user/countrieslist', ['as' => 'user.countriesList',
                                  'uses' => 'UserController@countriesList']);
Route::post('user/citieslist', ['as' => 'user.citiesList',
                                'uses' => 'UserController@citiesList']);
Route::get('user/wishlist/{id}', ['as' => 'user.wishlist',
                                  'uses' => 'UserController@wishListShow']);
Route::post('user/avatarImage', ['as' => 'user.avatarImage',
                                'uses' => 'UserController@getAvatarImage']);
Route::post('user/register', ['as' => 'register', 
                              'uses' => 'UserController@register']);

Route::resource('user', 'UserController',
                ['only' => ['index', 'store', 'update', 'destroy', 'show']]);

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
