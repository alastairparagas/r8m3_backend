<?php

use LaravelBook\Ardent\Ardent;

class Image extends Ardent{
    
    // Table in MySQL
    protected $table = 'images';
    protected $guarded = array('created_at', 'updated_at');
    
    // When saving or inserting rules, this must be followed
    public static $rules = array(
        'user_id' => 'required|alpha_num',
        'id' => 'required|unique:images,id',
        'file' => 'required|unique:images,file'
    );
    // Match input name when validating data to Model's columns
    public $autoHydrateEntityFromInput = false;
    // Hydrates auto-magically whenever validation is called - for updating
    public $forceEntityHydrationFromInput = false;
    
	// Belongs to User Model
    public function user(){
        $this->belongsTo('User');
    }
    
}
