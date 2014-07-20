<?php

class ImageTableSeeder extends Seeder{
    
    public function run(){
        
        $faker = Faker\Factory::create();
        
        for($i=0; $i<90; $i++){
            Image::create(array(
                'id' => $faker->randomNumber(5),
                'user_id' => "guest",
                'file' => $faker->imageUrl(1000, 1000, 'cats'),
                'rating' => $faker->numberBetween(0, 10),
                'raters_count' => $faker->randomNumber()
            ));
        }
        
    }
    
}

