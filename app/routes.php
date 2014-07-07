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
    return Redirect::route('login');
});

Route::get('/login', array(
    'as' => 'login',
    'uses' => 'AccountController@login'
));

// Routes that need loggin in before utilization
Route::group(array('before' => 'auth'), function() {
    
});

// API routes
Route::group(array('prefix' => 'api/v1'), function() {

    Route::any('/user/add', array(
        'as' => 'user-add-json',
        'uses' => 'UserApiController@addUser'
    ));
	
	Route::any('/user/login', array(
		'as' => 'user-login-json',
		'uses' => 'UserApiController@loginUser'
	));
	
	Route::any('/image/add-guest', array(
		'as' => 'image-guest-add-json',
		'uses' => 'ImageApiController@addImage'
	));

    Route::group(array('before' => 'auth.basicOnce'), function() {

        // View user's information including images associated to him
        Route::any('/user/view', array(
            'as' => 'user-view-json',
            'uses' => 'UserApiController@viewUser'
        ));

        // Edit the current HTTP Basic logged in user.
        Route::any('/user/edit', array(
            'as' => 'user-edit-json',
            'uses' => 'UserApiController@editUser'
        ));
		
		// Add POST-ed image to server with other information (Non-guest user)
		Route::any('/image/add', array(
			'as' => 'image-add-json',
			'uses' => 'ImageApiController@addImage'
		));

        // View images
        Route::any('/image/view', array(
            'as' => 'image-view-json-multiple',
            'uses' => 'UserApiController@viewImages'
        ));
        
        // View single image
        Route::any('/image/{image}/view', array(
           'as' => 'image-view-json' ,
            'uses' => 'ImageApiController@viewImage'
        ));

        // Edit information for this image
        Route::any('/image/{image}/edit', array(
            'as' => 'image-edit-json',
            'uses' => 'ImageApiController@editImage'
        ));

        // Delete the image
        Route::any('/image/{image}/delete', array(
            'as' => 'image-delete-json',
            'uses' => 'ImageApiController@deleteImageJson'
        ));
        
    });
});
