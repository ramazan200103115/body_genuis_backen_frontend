<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\Question;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $questions = Question::all();

        foreach ($questions as $question) {
            $correctOptionIndex = $faker->numberBetween(0, 3); // This will choose one correct option

            for ($i = 0; $i < 4; $i++) {
                Option::create([
                    'question_id' => $question->id,
                    'option' => $faker->sentence,
                    'is_right_option' => $i === $correctOptionIndex ? 1 : 0 // Only one option will be the correct one
                ]);
            }
        }
    }
}
