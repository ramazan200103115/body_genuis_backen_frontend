<?php

namespace App\Http\Controllers\Backend;

use App\Models\Quiz;
use App\Models\User;
use App\Models\UserParticipant;
use App\Models\UserQuestionAnswer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{

    public function index()
    {

        $currentYear = now()->year;
        $previousYear = now()->subYear()->year;

        // Generate month names with years for the dropdown
        $months = [];
        foreach ([$previousYear, $currentYear] as $year) {
            for ($month = 1; $month <= 12; $month++) {
                $months[] = now()->setYear($year)->setMonth($month)->format('F, Y');
            }
        }
        $data = [
            'totalUsers' => User::count(),
            'totalQuizzes' => Quiz::count(),
            'newUsers' => User::where('created_at', '>', now()->startOfMonth())->count(),
            'newQuizzes' => Quiz::where('created_at', '>', now()->startOfMonth())->count(),
            'selectedMonth' => now()->format('F, Y'),
            'months' => $months
        ];

        $questionAnswers = UserQuestionAnswer::with('questions.options')
            ->paginate(15);


        // foreach($questionAnswers as $key => $uqa) {
        //     $questions = Question::where('id', $uqa->questions->id)->first();
        //     $data[$key]['question'] = $questions->question;

        //     foreach($uqa->questions->options as $qtn) {
        //         $options = Option::where('question_id', $questions->id)->get();
        //         foreach($options as $option) {
        //             if ($option->is_right_option == 1) {
        //                 $data[$key]['right_options'] = $option->option;
        //             }
        //         }
        //     }
        // }

        foreach ($questionAnswers as $questions) {
            // dd($questions->questions);
            // $userAnswer = Option::where('id', $questions->option_id)
            //                         ->where('question_id', $questions->question_id)
            //                         ->get();

            // foreach($userAnswer as $answer) {
            //     $userQuestionAnswers['user_option'] = $userAnswer->option;
            //     $userQuestionAnswers['is_right_option'] = $userAnswer->is_right_option;

            // }
        }

        // dd($questionAnswers);

        return view('backend.index', [
            'questionAnswers' => $questionAnswers,
            'totalUsers' => User::count(),
            'totalQuizzes' => Quiz::count(),
            'newUsers' => User::where('created_at', '>', now()->startOfMonth())->count(),
            'newQuizzes' => Quiz::where('created_at', '>', now()->startOfMonth())->count(),
            'selectedMonth' => now()->format('F, Y'),
            'months' => $months,
            'averageAge' => (int)User::avg('age'),
            'averageScore' => (int)UserParticipant::avg('score'),
        ]);
    }
}
