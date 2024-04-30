<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Quiz;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $quizzes = Quiz::all();

        foreach ($quizzes as $quiz) {
            for ($i = 0; $i < 20; $i++) {
                Question::create([
                    'quiz_id' => $quiz->id,
                    'question' => $faker->sentence . '?',
                    'image_url' => $faker->imageUrl,
                ]);
            }
        }
    }
}
