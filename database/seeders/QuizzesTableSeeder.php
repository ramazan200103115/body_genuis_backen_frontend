<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuizzesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            Quiz::create([
                'title' => $faker->sentence,
                'author' => $faker->randomElement([1, 4, 5]),
                'questions_count' => 20,
                'is_active' => $faker->boolean,
                'is_private' => $faker->boolean,
                'code' => $faker->regexify('[A-Z0-9]{8}'),
            ]);
        }
    }
}
