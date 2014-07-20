<?php

use LaravelBook\Ardent\Ardent;

class Rate extends Ardent{
    
    // Table in MySQL
    protected $table = 'rates';
    protected $guarded = array('created_at', 'updated_at');
    public $incrementing = false;
    
    // When saving or inserting rules, this must be followed
    public static $rules = array(
        'user_id' => 'required|exists:users,id',
        'image_id' => 'required|exists:images,id'
    );
    // Match input name when validating data to Model's columns
    public $autoHydrateEntityFromInput = false;
    // Hydrates auto-magically whenever validation is called - for updating
    public $forceEntityHydrationFromInput = false;
    
    // Belongs to User Model, looks for user_id in this column
    // This Rate belongs to the user who made such a score
    public function user(){
        return $this->belongsTo('User');
    }
    
    // Belongs to Image Model, looks for image_id in this column
    // This Image belongs to the image who was rated
    public function image(){
        return $this->belongsTo('Image');
    }
    
}
