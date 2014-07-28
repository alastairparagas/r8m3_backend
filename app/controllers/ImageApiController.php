<?php

/**
 * ImageController handles all of our image manipulation. API endpoints for
 * this controller and their respective methods are:
 *      image/add -> add()
 *      image/add-guest -> add()
 *      image/view -> viewMultiple()
 *      image/:id/rate -> rate($image_id)
 *      image/:id/view -> viewOne($image_id)
 *      image/:id/extend -> extendExpiration($image_id)
 *      image/:id/delete -> delete($image_id)
 */

class ImageApiController extends BaseController {
    
    /**
     * Gives a list of images currently in the server, delimited by a limit and orderBy GET parameter
     */
    public function viewMultiple(){
        // Default configuration
        $orderBy = Input::get('orderBy') ? Input::get('orderBy') : 'created_at';
        $limit = Input::get('limit') ? Input::get('limit') : 30;
        Cache::add('maxStartAt', DB::table('images')->count() - $limit, 10);
        
        if(Input::has('startAt')){
             $startAt = Input::get('startAt');
        }else{
            if(Cache::get('maxStartAt') > 0){
               $startAt = rand(1, Cache::get('maxStartAt'));
            }else{
                $startAt = 0;
            }
        }
    
        $images = Image::with('rates')->take($limit)->skip($startAt)->orderBy($orderBy)->get();
        
	if($images->count() == 0){
		return $this->jsonResponse("error", "No images found with given parameters", Input::get('callback'));
	}
		
	return $this->jsonResponse("ok", "Images found", Input::get('callback'), $images);
    }
    
    /**
     * Returns information about the image, include URL to image file
     * @param type $image_id identifier of image being viewed
     */
    public function viewOne($image_id){
        $image = Image::find($image_id);
        
        if(empty($image)){
            return $this->jsonResponse("error", "Cannot find image", Input::get('callback'));
        }
        
        return $this->jsonResponse("ok", "Image found", Input::get('callback'), $image->first());
    }
    
    /**
     * Adds an image along with other passed information associated 
     * with the image to the database for it to be rated upon by other
     * rateMe users.
     */
    public function add(){
        $uploaded_image = Input::file('image_file_actual');
        $allowedTypes = array("image/jpeg", "image/png", "image/jpg");
        
    	if( !Input::hasFile('image_file_actual') ){
            return $this->jsonResponse("error", "No image provided", Input::get('callback'));
    	}
        if( !in_array($uploaded_image->getMimeType(),$allowedTypes) ){
            return $this->jsonResponse("error", "Uploaded invalided file type", Input::get('callback'));
        }
    	
        $image = new Image;
        $image->user_id = isset(Auth::user()->id) ? Auth::user()->id : "guest";
        $image->id = strtoupper(str_random("5"));
	while( isset(Image::find($image->id)->file) ){
		$image->id = strtoupper(str_random("5"));
	}
        $imageName = $image->id . "." . $uploaded_image->getClientOriginalExtension();	
	$image->file = URL::asset('images/'.$imageName);
        $image->rating = 0;
        $image->raters_count = 0;
        
        if(!$image->save()){
            return $this->jsonResponse("error", "Cannot add image", Input::get('callback'), $image->errors());
        }
        	
        $uploaded_image->move(public_path('images'), $imageName);
        
        if(!empty(Auth::user()->id)){
            $user = User::find(Auth::user()->id);
            $user->uploaded_count = $user->uploaded_count + 1;
            $user::$rules['password'] = '';
            if(!$user->updateUniques()){
                return $this->jsonResponse("error", "Unable to increase upload_count of user", Input::get('callback'), $user->errors());
            }
        }
        
        return $this->jsonResponse("ok", "Succesfully added image", Input::get('callback'), $image);
    }
    
    /**
     * Prolongs the image's survival date given the identifier of image
     * simply by updating their updated_at date. A background cron will run every
     * day to eliminate pictures that have been up for 7 days.
     * @param type $image_id identifier of image whose expiration is being extended
     */
    public function extendExpiration($image_id){
        $image = Image::find($image_id);
        
        if(empty($image)){
            return $this->jsonResponse("error", "Image does not exist", Input::get('callback'));
        }
        $image->updated_at = date('Y-m-d G:i:s');
        // Update timestamp
        if(!$image->updateUniques()){
            return $this->jsonResponse("error", "Unable to save image edits", Input::get('callback'), $image->errors());
        }
        
        return $this->jsonResponse("ok", "Successfully extended expiration", Input::get('callback'));
    }
    
    /**
     * Deletes an image given an identifier for that image.
     * @param type $image_id identifier of image being deleted
     */
    public function delete($image_id){
        $image = Image::find($image_id);
        if(empty($image)){
            return $this->jsonResponse("error", "Cannot find image", Input::get('callback'));
        }
        if($image->user_id != Auth::user()->id){
            return $this->jsonResponse("error", "Image owned by another user. Cannot delete image.", Input::get('callback'));
        }
        
        // Delete Image File
        File::delete(public_path() . "/images/" . basename($image->file));
        
        // Delete SQL record
        if(!$image->delete()){
            return $this->jsonResponse("error", "Cannot delete image", Input::get('callback'), $image->errors());
        }
        
        return $this->jsonResponse("ok", "Image deleted", Input::get('callback'));
    }
    
    /**
     * Rates the image given an identifier for that image
     * @param type $image_id identifier of image being rated
     */
    public function rate($image_id){
        $image = Image::find($image_id);
        $existingRate = Rate::where('user_id', '=', Auth::user()->id)->where('image_id', '=', $image_id)->get();
        $score = Input::get('score');
        
        if(empty($image)){
            return $this->jsonResponse("error", "Cannot find image", Input::get('callback'));
        }
        
        if(empty($score)){
            return $this->jsonResponse("error", "No score provided", Input::get('callback'));
        }
        
        if($score < 1 || $score > 10){
            return $this->jsonResponse("error", "Not a valid score", Input::get('callback'));
        }
        
        if(count($existingRate) > 0){
            return $this->jsonResponse("error", "You already rated the image", Input::get('callback'));
        }
        
        // Calculate the new score of the image.
        // Image Rating = all score points / amount of people who scored images
        // Multipling raters_count returns only all score points, to which
        // we can add the given score and divide the new compounded points with
        // the new count of population (old population + 1)
        $image->rating = $image->rating > 0 ? (($image->rating * $image->raters_count) + $score) / ($image->raters_count + 1) : $score;
        $image->raters_count = $image->raters_count + 1;
        if(!$image->updateUniques()){
            return $this->jsonResponse("error", "Cannot save score to image", Input::get('callback'), $image->errors());
        }
        
        $rate = new Rate;
        $rate->user_id = isset(Auth::user()->id) ? Auth::user()->id : "guest";
        $rate->image_id = $image_id;
        $rate->score = $score;
        if(!$rate->save()){
            return $this->jsonResponse("error", "Cannot save lone score", Input::get('callback'), $rate->errors());
        }
        
        return $this->jsonResponse("ok", "Image successfully scored.", Input::get('callback'), $image);
        
    }
    
    
}