<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with('creator:id,name') // Adjust 'id,name' based on your author's table
        ->select('id', 'title', 'author', 'code', 'questions_count', 'image_url')->where('is_private', 0)
            ->get();

        // Transform each quiz, including converting the author ID to an author name
        $transformedQuizzes = $quizzes->map(function ($quiz) {
            return [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'author' => $quiz->creator->name ?? 'Unknown', // Fallback to 'Unknown' if not found
                'questions_count' => $quiz->questions_count,
                'code' => $quiz->code,
                'image_url' => $quiz->image_url ? url('storage/' . $quiz->image_url) : null,
            ];
        });

        return $this->sendResponse(['quizzes' => $transformedQuizzes], 200);
    }

    public function questions(Request $request)
    {
        if (!empty($request->quiz_id)) {
            $questions = Question::with('options')->where('quiz_id', $request->quiz_id)->get();
        } elseif (!empty($request->education_id)) {
            $questions = Question::with('options')->where('education_id', $request->education_id)->get();
        } else {
            return $this->sendError('Quiz or Education id required', 401);
        }

        $transformedQuestions = $questions->map(function ($question) {
            $options = $question->options->pluck('option');
            $correctOptionIndex = $question->options->search(function ($option) {
                return $option->is_right_option == 1;
            });

            return [
                'question' => $question->question,
                'options' => $options,
                'answer_index' => $correctOptionIndex,
                'imageUrl' => $question->image_url ? $question->image_url : 'null', // Assuming 'null' as a string is intentional
            ];
        });

        $response = [
            'questions' => $transformedQuestions
        ];
        return response()->json($response);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'quiz_id' => 'required|exists:quizzes,id',
            'score' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['message' => 'Validation failed', 'errors' => $errors], 422);
        }

        $attributes = ['user_id' => $request->user_id, 'quiz_id' => $request->quiz_id];
        $values = [
            'score' => $request->score,
        ];

        $userParticipant = UserParticipant::updateOrCreate($attributes, $values);

        if ($userParticipant) {
            return $this->sendResponse('Success', 200);
        } else {
            return $this->sendError('Error', 403);
        }
    }

    public function getByCode($code)
    {
        $quizzes = Quiz::with('creator:id,name') // Adjust 'id,name' based on your author's table
        ->select('id', 'title', 'author', 'code', 'questions_count', 'image_url')
            ->where('code', $code)->get();

        // Transform each quiz, including converting the author ID to an author name
        $transformedQuizzes = $quizzes->map(function ($quiz) {
            return [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'author' => $quiz->creator->name ?? 'Unknown', // Fallback to 'Unknown' if not found
                'questions_count' => $quiz->questions_count,
                'code' => $quiz->code,
                'image_url' => $quiz->image_url ? url($quiz->image_url) : null,
            ];
        });

        return response()->json($transformedQuizzes);
    }
}
