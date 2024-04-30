<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\User;
use App\Models\UserParticipant;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserParticipantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $users = User::all();
        $quizzes = Quiz::all();

        foreach ($users as $user) {
            foreach ($quizzes as $quiz) {
                UserParticipant::create([
                    'user_id' => $user->id,
                    'quiz_id' => $quiz->id,
                    'score' => $faker->numberBetween(60,100),
                    'participation_status' => $faker->randomElement(['participated', 'not participated'])
                ]);
            }
        }
    }
}
