<?php

namespace Database\Seeders;
use Carbon\Carbon;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;




class AdsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create(); // Create a Faker instance

         // Using Eloquent to insert ads
        $ads = [
            'page_id' => '11111',
            'library_id' => $faker->unique()->randomNumber(), // Generate a unique random ID
            'page_name' => 'adel bouchaib',
            'page_url' => 'https://example.com/',
            'copy' => 'this is a copy',
            'description' => 'this is a description',
            'headline' => 'this is a headline',
            'cta' => 'this is a cta',
            'url' => 'https://example.com/', 
            'creative_type' => '1',
            'creative_url' => 'https://example.com/',
            'thumbnail_url' => 'https://example.com/', 
            'starting_date' => '1736150400', 
            'active_time' => '134', 
            'count' => '[{"date": "2025-01-08", "number": 200},{"date": "2025-01-09", "number": 10},{"date": "2025-01-10", "number": 30},{"date": "2025-01-11", "number": 30},{"date": "2025-01-12", "number": 30},{"date": "2025-01-13", "number": 30},{"date": "2025-01-14", "number": 10}]', 
            'today_count' => '10'
        ];


        for ($i = 1; $i < 2; $i++) {
            \App\Models\Ad::create($ads);
        }
    }
}
