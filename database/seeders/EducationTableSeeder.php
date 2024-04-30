<?php

namespace Database\Seeders;

use App\Models\Education;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EducationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) { // Assuming you want to create 5 records
            Education::create([
                'title' => $faker->words(3, true), // Generates a string of 3 words
                'info' => $faker->paragraphs(3, true), // Generates a string of 3 paragraphs
                'diseases' => $faker->words(5, true), // Generates a string of 5 words
            ]);
        }
    }
}
