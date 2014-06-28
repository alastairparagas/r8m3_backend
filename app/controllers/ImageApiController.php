<?php

class ImageApiController extends BaseController {
    
    /**
     * Gives a list of images currently in the server, delimited by a limit and orderBy GET parameter
     * @return type JSON response of results
     */
    public function viewImages(){
        // Default configuration
        $orderBy = !empty(Input::get('orderBy')) ? Input::get('orderBy') : 'created_at';
        $limit = !empty(Input::get('limit')) ? Input::get('limit') : '10';
    
        
        
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
        $image = new Image;
        $image->user_id = Auth::user()->id;
        $image->id = time().str_random("5");
        $image->rating = 0;
        $image->raters_count = 0;
        
        if(!$image->save()){
            return $this->jsonResponse("error", "Cannot add image", Input::get('callback'), $image->errors());
        }
        
        return $this->jsonResponse("ok", "Succesfully registered", Input::get('callback'), $image);
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

