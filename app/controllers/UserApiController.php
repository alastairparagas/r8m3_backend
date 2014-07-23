<?php

/**
 * UserApiController handles all of our user data as well as user-centric methods. 
 * API endpoints for this controller and their respective methods are:
 *      user/view -> view()
 *      user/add -> add()
 *      user/edit -> edit()
 *      user/login -> login()
 *      user/images -> images()
 * All the return types default to JSON, which are transmitted to our mobile
 * and web applications.
 */

class UserApiController extends BaseController {
    
    /**
     * Returns information about current logged in User (HTTP Auth basic login)
     */
    public function view(){
        $user = User::where('username', '=', Auth::user()->username)->get();
        if(count($user) != 1){
            return $this->jsonResponse("error", "User does not exist", Input::get('callback'));
        }
        return $this->jsonResponse("ok", "User Details", Input::get('callback'), $user);
    }
    
    /**
     * Adds a User in our Database
     */
    public function add() {
        $user = new User;
        $user->forceEntityHydrationFromInput = true;
        // Ardent Model automatically hydrates fields
        if (!$user->save()) {
            return $this->jsonResponse("error", "Cannot add user", Input::get('callback'), $user->errors());
        }
        
        return $this->jsonResponse("ok", "Succesfully registered", Input::get('callback'), $user);
    }

    /**
     * Edits the current logged in User (HTTP Auth basic login)
     */
    public function edit() {
        $user = User::find(Auth::user()->id);
        
        if(empty($user)){
            return $this->jsonResponse("error", "User does not exist", Input::get('callback'));
        }
        
        $user->forceEntityHydrationFromInput = true;
        $user::$rules['password'] = '' ;
        
        if(!$user->updateUniques()){
            return $this->jsonResponse("error", "Unable to save profile edits", Input::get('callback'), $user->errors());
        }
        
        return $this->jsonResponse("ok", "Succesfully saved profile edits", Input::get('callback'));
    }
	
    /**
    * Logs the user in with our basic auth, returns whether it was successful or not.
    */
    public function login(){
	if(Auth::attempt(array('username' => Input::get('username'), 'password' => Input::get('password')), true)){
            return $this->jsonResponse("ok", "Successfully logged in", Input::get('callback'));
	}else{
            return $this->jsonResponse("error", "Incorrect credentials/User does not exist", Input::get('callback'));
	}
    }
    
    /**
     * Returns the images made by the user.
     */
    public function images(){
        if(empty(Auth::user()->username)){
            return $this->jsonResponse("error", "You currently have no account.", Input::get('callback'));
        }
        
        $images = User::where('username', '=', Auth::user()->username)->first()->images()->get();
        
        if($images->count() == 0){
            return $this->jsonResponse("error", "User has no images", Input::get('callback'));
        }
        
        return $this->jsonResponse("ok", "User has images", Input::get('callback'), $images);
    }

}
