<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */

Route::get('/', function() {
    return Redirect::route('user-login-json');
});

// API routes
Route::group(array('prefix' => 'api/v1'), function() {

    Route::any('/user/add', array(
        'as' => 'user-add-json',
        'uses' => 'UserApiController@add'
    ));
	
    Route::any('/user/login', array(
        'as' => 'user-login-json',
	'uses' => 'UserApiController@login'
    ));
	
    Route::any('/image/add-guest', array(
	'as' => 'image-guest-add-json',
	'uses' => 'ImageApiController@add'
    ));
    
    // View images
    Route::any('/image/view', array(
        'as' => 'image-view-json-multiple',
        'uses' => 'ImageApiController@viewMultiple'
    ));

    Route::group(array('before' => 'auth.basicOnce'), function() {

        // View user's information including images associated to him
        Route::any('/user/view', array(
            'as' => 'user-view-json',
            'uses' => 'UserApiController@view'
        ));

        // Edit the current HTTP Basic logged in user.
        Route::any('/user/edit', array(
            'as' => 'user-edit-json',
            'uses' => 'UserApiController@edit'
        ));
        
        // User's bunch of images
        Route::any('/user/images', array(
           'as' => 'users-images-json',
           'uses' => 'UserApiController@images'
        ));
		
	// Add POST-ed image to server with other information (Non-guest user)
	Route::any('/image/add', array(
		'as' => 'image-add-json',
		'uses' => 'ImageApiController@add'
	));
        
        // View single image
        Route::any('/image/{image}/view', array(
           'as' => 'image-view-json' ,
            'uses' => 'ImageApiController@viewOne'
        ));
        
        Route::any('/image/{image}/rate', array(
           'as' => 'image-rate-json',
            'uses' => 'ImageApiController@rate'
        ));

        // Edit information for this image
        Route::any('/image/{image}/extend', array(
            'as' => 'image-edit-json',
            'uses' => 'ImageApiController@extendExpiration'
        ));

        // Delete the image
        Route::any('/image/{image}/delete', array(
            'as' => 'image-delete-json',
            'uses' => 'ImageApiController@delete'
        ));
        
    });
});
