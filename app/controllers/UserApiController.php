<?php

class UserApiController extends BaseController {
    
    /**
     * Returns information about current logged in User (HTTP Auth basic login)
     * @return type JSON response of $results
     */
    public function viewUser(){
        $user = User::where('username', '=', Auth::user()->username);
        if($user->count() != 1){
            return $this->jsonResponse("error", "User does not exist", Input::get('callback'));
        }
        return $this->jsonResponse("ok", "User Details", Input::get('callback'), $user->first());
    }
    
    /**
     * Adds a User in our Database
     * @return type JSON response of results
     */
    public function addUser() {
        $user = new User;
        // Ardent Model automatically hydrates fields
        if (!$user->save()) {
            return $this->jsonResponse("error", "Cannot add user", Input::get('callback'), $user->errors());
        }
        
        return $this->jsonResponse("ok", "Succesfully registered", Input::get('callback'), $user);
    }

    /**
     * Edits the current logged in User (HTTP Auth basic login)
     * @return type JSON response of results
     */
    public function editUser() {
        $user = User::where('username', '=', Auth::user()->username);

        if($user->count() != 1){
            return $this->jsonResponse("error", "User does not exist", Input::get('callback'));
        }
        
        $user = $user->first();
        $user::$rules['password'] = (Input::get('password')) ? 'required' : '' ;
        
        if(!$user->updateUniques()){
            return $this->jsonResponse("error", "Unable to save profile edits", Input::get('callback'), $user->errors());
        }
        
        return $this->jsonResponse("ok", "Succesfully saved profile edits", Input::get('callback'));
    }

}
