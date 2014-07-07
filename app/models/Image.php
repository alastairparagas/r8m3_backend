<?php

use LaravelBook\Ardent\Ardent;

class Image extends Ardent{
    
    // Table in MySQL
    protected $table = 'images';
    protected $guarded = array('created_at', 'updated_at');
    
    // When saving or inserting rules, this must be followed
    public static $rules = array(
        'user_id' => 'required|numeric|exists:users,id',
        'id' => 'required|unique:images,id',
        'file' => 'required|unique:images,file|mimes:jpeg,jpg,png',
    );
    // Match input name when validating data to Model's columns
    public $autoHydrateEntityFromInput = true;
    // Hydrates auto-magically whenever validation is called - for updating
    public $forceEntityHydrationFromInput = true;
    
	// Belongs to User Model
    public function user(){
        $this->belongsTo('User');
    }
    
}
