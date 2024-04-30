<?php

namespace App\Http\Controllers\Backend;

use App\Models\Quiz;
use App\Models\User;
use App\Models\UserParticipant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchUserController extends Controller
{
    public function search(Request $request)
    {
        $term = $request->name;

        $users = User::where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('email', 'LIKE', '%' . $term . '%')->get();
        $selectedUser = $users->first();

        if ($request->type == 0) {
            $selectedUser->average_score = (int)UserParticipant::where('user_id', $selectedUser->id)->avg('score');
            $selectedUser->passed_quizzes = (int)UserParticipant::where('user_id', $selectedUser->id)->count();
            return view('backend.users.indexUsers')->with([
                'users' => $users,
                'selectedUser' => $selectedUser
            ]);
        } else {
            $passingScore = 0;

            $totalPassedStudents = Quiz::where('author', $selectedUser->id)
                ->withCount(['participants' => function ($query) use ($passingScore) {
                    $query->where('score', '>=', $passingScore);
                }])
                ->get()
                ->sum('participants_count');


            $quizzes = Quiz::where('author', $selectedUser->id)->with('participants')->get();

            $overallAverageScore = $quizzes->pluck('participants')
                ->flatten() // Flatten the collection of collections
                ->avg('score'); // Directly compute the average of scores from all participants of all quizzes

            $publicQuizzesCount = Quiz::where('is_private', false)->where('author', $selectedUser->id)->count();

            $selectedUser->totalPassedStudents = (int)$totalPassedStudents;
            $selectedUser->averageScores = (int)$overallAverageScore;
            $selectedUser->publicQuizzesCount = (int)$publicQuizzesCount;
            return view('backend.users.indexTeachers')->with([
                'users' => $users,
                'selectedUser' => $selectedUser
            ]);
        }
    }

}
