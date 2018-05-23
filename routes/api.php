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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//GALLERY ROUTES
Route::group(['prefix' => 'gallery', 'namespace' => 'Api'], function() {
    Route::get('/', 'GalleryController@index')->name('api.gallery.index');
    Route::post('/', 'GalleryController@store')->name('api.gallery.store');
    Route::get('{id}', 'GalleryController@show')->name('api.gallery.show');
    Route::put('{id}', 'GalleryController@update')->name('api.gallery.update');
    Route::put('/status/{id}', 'GalleryController@status')->name('api.gallery.status');
    Route::delete('{id}', 'GalleryController@destroy')->name('api.gallery.destroy');

    //PHOTO ROUTES
    Route::group(['prefix' => 'photo'], function() {
        Route::post('/upload', 'PhotoController@uploadPhotos')->name('api.gallery.photo.upload');
        Route::put('/update', 'PhotoController@update')->name('api.gallery.photo.update');
        Route::put('/update-attr', 'PhotoController@updateAttr')->name('api.gallery.photo.updateAttr');
        Route::put('/order', 'PhotoController@order')->name('api.gallery.photo.order');
        Route::put('/status/{id}', 'PhotoController@status')->name('api.photo.status');
        Route::delete('{id}', 'PhotoController@destroy')->name('api.gallery.photo.destroy');
    });
});