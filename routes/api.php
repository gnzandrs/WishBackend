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
Route::post('user/avatarImage', ['as' => 'user.avatarImage',
                                'uses' => 'UserController@getAvatarImage']);
Route::post('user/register', ['as' => 'register',
                              'uses' => 'UserController@register']);
Route::post('user/login', ['as' => 'user.login',
                          'uses' => 'Auth\LoginController@authenticate']);                                                                                     

Route::resource('user', 'UserController',
                ['only' => ['index', 'store', 'update', 'destroy', 'show']]);


Route::get('category', ['as' => 'category.list',
    'uses' => 'CategoryController@getCategories']);


Route::get('category/{name}', ['as' => 'category.show',
    'uses' => 'CategoryController@show']);


Route::get('location/getmarkers', ['as' => 'location.getmarkers',
    'uses' => 'LocationController@getMarkers']);

Route::get('location/search/{lat}/{lng}', ['as' => 'location.search',
    'uses' => 'LocationController@search']);

Route::get('location/getmarkers', 'LocationController@getMarkers');

Route::get('wishlist/{id}', 'WishListController@show');



Route::group(['middleware' => 'jwt-auth'], function () {
    Route::post('wish', 'WishController@store');
    Route::post('wish/imageupload', 'WishController@imageUpload');
    Route::post('wish/delete-image', 'WishController@imageDelete');
    Route::post('wish/create-wish-directory', 'WishController@createWishDirectory');

    Route::post('wishlist', 'WishListController@store');
    Route::post('wishlist/create-image-directory', 'WishListController@createImageDirectory');


    Route::post('location', 'LocationController@store');
});


/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
