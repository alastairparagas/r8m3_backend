<?php

use LaravelBook\Ardent\Ardent;

class Image extends Ardent{
    
    // Table in MySQL
    protected $table = 'images';
    protected $guarded = array('created_at', 'updated_at');
    public $incrementing = false;
    
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
    
    // User Model has a bunch of personally uploaded images. First
    // Parameter is this Model's parent, second parameter is the key that
    // represents the first Model's identifier (defaults to mode_id so in this
    // case, it would be user_id, and third is the name of the identifier in
    // the parent table)
    public function user(){
        return $this->belongsTo('User');
    }
    
    public function rates(){
        return $this->hasMany('Rate');
    }
    
    public function ratees(){
        return $this->hasMany('Rate')->select(array('id', 'user_id'));
    }
    
}
