<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use LaravelBook\Ardent\Ardent;

class User extends Ardent implements UserInterface {

    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     * @var array
     */
    protected $hidden = array('password', 'remember_token');
    protected $guarded = array('id', 'created_at', 'updated_at');
    
    // When saving or inserting rules, this must be followed
    public static $rules = array(
        'username' => 'required|between:3,16|unique:users,username',
        'password' => 'required|between:5,16',
        'email' => 'required|email|unique:users,email'
    );
    // Match input name when validating data to Model's columns
    public $autoHydrateEntityFromInput = true;
    // Hydrates auto-magically whenever validation is called - for updating
    public $forceEntityHydrationFromInput = true;
    
    // Hash this input name automatically
    public static $passwordAttributes = array('password');
    public $autoHashPasswordAttributes = true;
    
    public function getAuthIdentifier() {
        return $this->getKey();
    }

    public function getAuthPassword() {
        return $this->password;
    }

    public function getReminderEmail() {
        return $this->email;
    }

    public function getRememberToken() {
        return $this->remember_token;
    }

    public function getRememberTokenName() {
        return 'remember_token';
    }

    public function setRememberToken($value) {
        $this->remember_token = $value;
    }
    
    public function image(){
        $this->hasMany('Image');
    }
    
}
