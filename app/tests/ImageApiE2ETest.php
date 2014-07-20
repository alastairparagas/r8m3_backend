<?php

/*
 * End-to-end tester for ImageApiController
 * This executes the job that is expected of a certain Image API endpoint
 * of our R8M3 service.
 * 
 * Tests these Image API endpoints:
 *      image/add
 *      image/add-guest
 *      image/view
 *      image/:id/rate
 *      image/:id/view
 *      image/:id/extend
 *      image/:id/delete
 */

class ImageApiE2ETest extends TestCase {
    
    private $domain;
    private $username;
    private $password;
    private $addedImages;
    private $existingImages;
    private $nonexistingImages;
    private $currentHour;
    
    public function setUp() {
        parent::setUp();
        // Root domain to use for testing API endpoints
        $this->domain = TestCase::$rootDomain;
        
        // Credentials used to authenticate in HTTP BASIC guarded areas
        $this->username = "apara1234";
        $this->password = "apara1234";
        
        // Location of test folder relative to root of this project
        $this->testLocation = "\\app\\tests\\";
        
        // Images that will be added in the course of the test
        $this->addedImages = array();
        
        // Images that already exist that we will be manipulating through the course of the test
        $this->existingImages = array("RQVLC", "DF8QB", "DCDMZ");
        $this->existingImagesInfo = array();
        // Store current data for these images so after we test, we can
        // restore them back to their pre-testing state (practically unchanged values)
        foreach($this->existingImages as $existingImage){
            $this->existingImagesInfo[$existingImage] = Image::find($existingImage);
        }
        
        $this->nonexistingImages = array("JohnDoe", "ShortCircuit", "EmpireAttack");
        $this->currentHour = date('Y n j G');
    }
    
    
    /**
     * Tests whether our endpoints are up and responding or not and whether
     * our Httpful dependency is working.
     */
    public function testCanApi() {
        $response = Httpful::get($this->domain)->send();
        $this->assertNotNull($response);
        $this->assertObjectHasAttribute('body', $response);
        $this->assertNotNull($response->body);
    }
    
    
    /**
     * Tests whether we can view images or not
     */
    public function testViewImages() {
        $response = Httpful::get($this->domain . 'image/view')->authenticateWith($this->username, $this->password)->send();
        $this->assertFalse($response->hasErrors());
        $this->assertEquals("application/json", $response->content_type);
    }
    
    
    /**
     * Tests if we can view one image
     */
    public function testViewImage() {
        // Checks response for existing images
        foreach($this->existingImages as $existingImage){
            $response = Httpful::get($this->domain . 'image/' . $existingImage . '/view')->authenticateWith($this->username, $this->password)->send();
            $this->assertFalse($response->hasErrors());
            $this->assertEquals("application/json", $response->content_type);
        }
        // Checks response for non-existing images
        foreach($this->nonexistingImages as $nonexistingImage){
            $response = Httpful::get($this->domain . 'image/' . $nonexistingImage . '/view')->authenticateWith($this->username, $this->password)->send();
            $this->assertTrue($response->hasErrors());
            $this->assertEquals("application/json", $response->content_type);
        }
    }
    
    
    /**
     * Tests if we can add an image
     */
    public function testAdd() {
        
        // Tests Image Upload fails as expected
        {
            // Test authenticated route works
            $response = Httpful::get($this->domain . 'image/add')->authenticateWith($this->username, $this->password)->send();
            $this->assertTrue($response->hasErrors());
            $this->assertEquals("application/json", $response->content_type);

            // Test authenticated route is guarded
            $response = Httpful::get($this->domain . 'image/add')->send();
            $this->assertTrue($response->hasErrors());
            $this->assertEquals("text/html", $response->content_type);
            $this->assertEquals("Invalid credentials.", $response->body);

            // Test guest route works
            $response = Httpful::get($this->domain . 'image/add-guest')->send();
            $this->assertEquals("application/json", $response->content_type);
            $this->assertTrue($response->hasErrors());
        }
        
        // Test Image Upload
        {
            $handle = opendir(app_path('tests/testFiles'));
            $allowedTypes = array('jpeg', 'jpg', 'png');
            while( ($file = readdir($handle)) !== FALSE ){
                if( $file == '.' || $file == '..'){
                    continue;
                }
                
                $actualFile = getcwd() . $this->testLocation . "testFiles\\" . $file;
                $actualFileInfo = pathinfo($actualFile);
                
                $fileArray = array('image_file_actual' => '@'.realpath($actualFile));
                $response = Httpful::post($this->domain . 'image/add', $fileArray, "multipart/form-data")->authenticateWith($this->username, $this->password)->expectsType("application/json")->send();
                
                if( in_array($actualFileInfo['extension'], $allowedTypes) ){
                    $this->assertEquals("application/json", $response->content_type);
                    $this->assertFalse($response->hasErrors());
                    $image = Image::find($response->body->info->id);
                    $this->assertNotNull($image);
                    $this->addedImages[] = $response->body->info->id;
                }else{
                    $this->assertEquals("application/json", $response->content_type);
                    $this->assertTrue($response->hasErrors());
                }                
            }
        }
    }
    
    
    /**
     * Tests if image expiration extension works
     */
    public function testExtendExpiration() {
        foreach($this->existingImages as $existingImage){
            $image = Image::find($existingImage);
            
            $response = Httpful::get($this->domain . 'image/' . $existingImage . '/extend')->authenticateWith($this->username, $this->password)->send();
            $this->assertEquals("application/json", $response->content_type);
            $this->assertFalse($response->hasErrors());
            
            // Check that database has been changed
            $newImage = Image::find($existingImage);
            $extensionYear = $newImage->updated_at->year;
            $extensionMonth = $newImage->updated_at->month;
            $extensionDay = $newImage->updated_at->day;
            $extensionHour = $newImage->updated_at->hour;
            $extensionDate = "$extensionYear $extensionMonth $extensionDay $extensionHour";
            $this->assertEquals($this->currentHour, $extensionDate);
        }
    }
    
    
    // Restore back changes made to the database
    public function tearDown(){
        parent::tearDown();
        // Restore back image information modified in this test
        foreach($this->existingImages as $existingImage){
            $oldImage = $this->existingImagesInfo[$existingImage];
            
            $restoredImage = Image::find($existingImage);
            $restoredImage->file = $oldImage->file;
            $restoredImage->rating = $oldImage->rating;
            $restoredImage->raters_count = $oldImage->raters_count;
            $restoredImage->updated_at = $oldImage->updated_at;
            $restoredImage->created_at = $oldImage->created_at;
            $restoredImage->updateUniques();
        }
        // Delete images that were added in this test
        foreach($this->addedImages as $addedImage){
            $image = Image::find($addedImage);
            File::delete(public_path() . "/images/" . basename($image->file));
            $image->delete();
        }
    }
    
}
