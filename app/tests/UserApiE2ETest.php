<?php

/*
 * End-to-end tester for UserApiController
 * This executes the job that is expected of a certain User API endpoint
 * of our R8M3 service.
 * 
 * Tests these User API endpoints:
 *      user/view
 *      user/add
 *      user/edit
 *      user/login
 *      user/images
 */

class UserApiE2ETest extends TestCase {
    
    private $users; // Users that exist in the database
    private $usersInfo; // Info of users that exist in the database
    private $nonUsers; // Users that do not exist in the database
    private $usersToAdd; // Users that we want to add in the database
    private $userToAddFail; // Users that we want to add but will fail
    
    public function setUp() {
        parent::setUp();
        
        $this->users = array(array("apara1234", "apara1234"));
        $this->nonUsers = array(array("robotchakachaka", "empiregoese"));
        $this->usersToAdd = array(array("gogotest", "gogagi", "gogotest@gmail.com"), array("gogotesting", "gogogagi", "gogotesting@test.com"));
        $this->usersToAddFail = array(array("gogogogogogogogogogo", "gogogogogogogogogogo"), array("gogogo"));
        
        foreach($this->users as $user){
            $this->usersInfo[$user[0]] = User::where("username", "=", $user[0])->first();
        }
    }
    
    // Tests if we can retrieve user's information
    public function testViewUser(){
        // Test route is guarded
        $response = Httpful::get($this->domain . "user/view")->send();
        $this->assertEquals("text/html", $response->content_type);
        $this->assertEquals("Invalid credentials.", $response->body);
        // Test we can check info for a user
        foreach($this->users as $user){
            $response = Httpful::get($this->domain . 'user/view')->authenticateWith($user[0], $user[1])->send();
            $this->assertEquals("application/json", $response->content_type);
            $this->assertFalse($response->hasErrors());
        }
    }
    
    // Tests if we can add a new person
    public function testAddUser(){
        foreach($this->usersToAdd as $user){
            $response = Httpful::post($this->domain . 'user/add', array('username' => $user[0], 'password' => $user[1], 'email' => $user[2]), "multipart/form-data")->send();
            $this->assertEquals("application/json", $response->content_type);
            $this->assertFalse($response->hasErrors());
            
            $checkUser = User::where('username', '=', $user[0])->get();
            $this->assertEquals(1, count($checkUser));
        }
        foreach($this->usersToAddFail as $user){
            $map = array("username", "password", "email");
            $preparedArray = array();
            for($i=0; $i<count($user); $i++){
                $preparedArray[$map[$i]] = $user[$i];
            }
            $response = Httpful::post($this->domain . 'user/add', $preparedArray, "multipart/form-data")->send();
            $this->assertEquals("application/json", $response->content_type);
            $this->assertTrue($response->hasErrors());
        }
    }
    
    // Tests if a person can be logged in
    public function testLogin(){
        foreach($this->users as $user){
            $response = Httpful::post($this->domain . 'user/login', array("username" => $user[0], "password" => $user[1]), "multipart/form-data")->send();
            $this->assertEquals("application/json", $response->content_type);
            $this->assertFalse($response->hasErrors());
        }
        foreach($this->nonUsers as $user){
            $response = Httpful::post($this->domain . 'user/login', array("username" => $user[0], "password" => $user[1]), "multipart/form-data")->send();
            $this->assertEquals("application/json", $response->content_type);
            $this->assertTrue($response->hasErrors());
        }
    }
    
    // Tests if we can view a person's images
    public function testImagesUser(){
        foreach($this->users as $user){
            $response = Httpful::get($this->domain . 'user/images')->authenticateWith($user[0], $user[1])->send();
            $this->assertEquals("application/json", $response->content_type);
            $usersImages = Image::where('user_id', '=', $this->usersInfo[$user[0]]->id)->get();
            
            if(count($usersImages) == 0){
                $this->assertTrue($response->hasErrors());
            }else{
                $this->assertFalse($response->hasErrors());
                $this->assertEquals(count($usersImages), count($response->body->info));
            }
        }
    }
    
    
    public function tearDown(){
        parent::tearDown();
        foreach($this->usersToAdd as $userAdded){
            $addedUser = User::where('username', '=', $userAdded[0]);
            $addedUser->delete();
        }
    }
    
    
}
?>