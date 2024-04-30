<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Models\Quiz;
use App\Models\UserParticipant;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchEducation(Request $request)
    {
        $term = $request->title;

        $educations = Education::select('id', 'title', 'created_at')->where('title', 'LIKE', '%' . $term . '%')
            ->withCount('questions') // This adds a `questions_count` attribute to each Education model
            ->get();
        $selectedEducation = $educations->first(); // As an example, select the first user
        return view('backend.education.index')->with([
            'educations' => $educations,
            'selectedEducation' => $selectedEducation
        ]);
    }

    public function searchQuiz(Request $request)
    {
        $term = $request->title;
        $quizs = Quiz::where('title', 'LIKE', '%' . $term . '%')->with('creator')->withCount('participants')->get();

        $selectedquiz = $quizs->first();

        $selectedquiz->participants_count = UserParticipant::where('quiz_id', $selectedquiz->id)->count();
        $selectedquiz->participants_average_score = (int)UserParticipant::where('quiz_id', $selectedquiz->id)->avg('score');
        $maxScore = UserParticipant::where('quiz_id', $selectedquiz->id)->max('score');
        $participantsWithMaxScore = UserParticipant::with('user')
            ->where('quiz_id', $selectedquiz->id)
            ->where('score', $maxScore)
            ->get();
        $champ = '';
        $champScore = '';
        foreach ($participantsWithMaxScore as $participant) {
            $champ = $participant->user->name . ", " . $participant->user->email;
            $champScore = $participant->score . "%      " . $participant->created_at;
        }
        $selectedquiz->champName = $champ;
        $selectedquiz->champScore = $champScore;
        return view('backend.quiz.index', ['quizzes' => $quizs, 'selectedquiz' => $selectedquiz]);

    }
}
