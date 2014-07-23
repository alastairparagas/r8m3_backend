<?php

class UserTableSeeder extends Seeder{
    
    public function run(){
        // Test Accounts we need to have constant access
        $user = new User;
        $user->username = "apara1234";
        $user->password = Hash::make("apara1234");
        $user->email = "alastairparagas@gmail.com";
        $user->save();
        
        $user = new User;
        $user->username = "test";
        $user->password = Hash::make("test");
        $user->email = "test@gmail.com";
        $user->save();
        
        // Generate random users
        $faker = Faker\Factory::create();
        for($i=0; $i<5; $i++){
            User::create(array(
               'username' => $faker->userName,
               'password' => $faker->email,
               'email' => $faker->word
            ));
        }
    }
    
}

?>

