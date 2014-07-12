<?php

class ImageApiController extends BaseController {
    
    /**
     * Gives a list of images currently in the server, delimited by a limit and orderBy GET parameter
     * @return type JSON response of results
     */
    public function viewImages(){
        // Default configuration
        $orderBy = Input::get('orderBy') ? Input::get('orderBy') : 'created_at';
        $limit = Input::get('limit') > 0 ? Input::get('limit') : '30';
		$startAt = Input::get('startAt') ? Input::get('startAt') : '1';
    
        $images = Image::take($limit)->orderBy($orderBy);
        
		if($images->count() == 0){
			return $this->jsonResponse("error", "No images retrieved with given parameters", Input::get('callback'));
		}
		
		return $this->jsonResponse("ok", "Images found", Input::get('callback'), $images);
    }
    
    /**
     * Returns information about the image, include URL to image file
     * @param type $image_id Identifier for image
     * @return type JSON response of results
     */
    public function viewImage($image_id){
        $image = Image::find($image_id);
        if($image->count() != '1'){
            return $this->jsonResponse("error", "Cannot find image", Input::get('callback'));
        }
        
        return $this->jsonResponse("ok", "Image found", Input::get('callback'), $image->first());
    }
    
    /**
     * Adds an image along with other passed information associated with the image
     * @return type JSON response of results
     */
    public function addImage(){
    	if(!Input::hasFile('image_file_actual')){
    		return $this->jsonResponse("error", "No image provided", Input::get('callback'));
    	}
    	
        $image = new Image;
        $image->user_id = isset(Auth::user()->id) ? Auth::user()->id : "guest";
        $image->id = time().str_random("5");
        $imageName = $image->id . "." . Input::file('image_file_actual')->getClientOriginalExtension();	
		$image->file = URL::asset('images/'.$imageName);
        $image->rating = 0;
        $image->raters_count = 0;
        
        if(!$image->save()){
            return $this->jsonResponse("error", "Cannot add image", Input::get('callback'), $image->errors());
        }
        	
        Input::file('image_file_actual')->move(public_path('images'), $imageName);
        return $this->jsonResponse("ok", "Succesfully added image", Input::get('callback'), $image);
    }
    
    /**
     * Edits the image's associated information given the identifier of image
     * @param type $image_id ID of image
     * @return type JSON response of results
     */
    public function editImage($image_id){
        $image = Image::find($image_id);
        
        if($image->count() != 1){
            return $this->jsonResponse("error", "Image does not exist", Input::get('callback'));
        }
        
        $image = $image->first();
        
        if(!$image->updateUniques()){
            return $this->jsonResponse("error", "Unable to save image edits", Input::get('callback'), $image->errors());
        }
        
        return $this->jsonResponse("ok", "Succesfully saved image edits", Input::get('callback'));
    }
    
    /**
     * Deletes an image given an identifier for that image.
     * @param type $image_id ID of image
     * @return type JSON response of results
     */
    public function deleteImage($image_id){
        $image = Image::find($image_id);
        if($image->count() != '1'){
            return $this->jsonResponse("error", "Cannot find image", Input::get('callback'));
        }
        
        if(!$image->delete()){
            return $this->jsonResponse("error", "Cannot delete image", Input::get('callback'));
        }
        
        return $this->jsonResponse("ok", "Image deleted", Input::get('callback'));
    }
    
}