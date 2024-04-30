<?php

namespace App\Http\Controllers\Backend;

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\UserParticipant;
use Faker\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Services\Quiz as SaveQuizOption;

class QuizController extends Controller
{
    public function __construct(
        Quiz     $quiz,
        Option   $option,
        Question $question
    )
    {
        $this->quiz = $quiz;
        $this->option = $option;
        $this->question = $question;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->hasRole('Super Admin')) {
            $quizs = $this->quiz->with('creator')->withCount('participants')->get();
        } else {
            $quizs = $this->quiz->where('author', auth()->id())->with('creator')->withCount('participants')->get();
        }
        if (!$quizs->isEmpty()) {
            if ($request->id) {
                $selectedquiz = $quizs->where('id', $request->id)->first();
                if (empty($selectedquiz)){
                    $selectedquiz = $quizs->first();
                }
            } else {
                $selectedquiz = $quizs->first();
            }
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
        } else {
            $quizs = [];
            $quizs = collect($quizs);

            $selectedquiz = [];
        }
        return view('backend.quiz.index', ['quizzes' => $quizs, 'selectedquiz' => $selectedquiz]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.quiz.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $faker = Factory::create();

        $quiz = new $this->quiz;
        $quiz->title = $request->name;
        $quiz->author = auth()->user()->id;
        $quiz->code = $faker->regexify('[A-Z0-9]{8}');
        $quiz->save();

        if ($request->hasFile('images')) {
            $file = $request->file('images');
            $path = $file->store('public');
            $fileName = basename($path);
            $quiz->image_url = $fileName;
            $quiz->save();
        }

        return redirect()->action([QuizController::Class, 'index']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $quiz = $this->quiz->where('id', $id)->firstOrFail();

        $questions = $this->question->with('options')
            ->where('quiz_id', $quiz->id)->get();

        return view('backend.quiz.edit', [
            'quiz' => $quiz,
            'questions' => $questions,
            'type' => 'choice'
        ]);
    }

    public function questions($id)
    {
        $quiz = $this->quiz->where('id', $id)->firstOrFail();

        $questions = $this->question->with('options')
            ->where('quiz_id', $quiz->id)->get();

        return view('backend.quiz.edit', [
            'quiz' => $quiz,
            'questions' => $questions,
            'type' => 'choice'
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        // DB::transaction(function() use ($slug, $request) {

        $this->validate($request, [
            'question' => 'required',
            'options.*' => 'required'
        ]);

        $type = $request->type;
        $quiz = $this->quiz->where('id', $id)->firstOrFail();

        //update question to db
        $question = new Question;
        $question->quiz_id = $quiz->id;
        $question->question = $request->question;
        $question->save();

        $quiz->questions_count = $quiz->questions_count + 1;
        $quiz->save();

        $saveOption = (new SaveQuizOption)->saveOptions($request, $question, $type);

        // });

        return redirect()->route('quiz.edit', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        DB::transaction(function () use ($request) {
            $quiz = $this->quiz->with('questions.options')->where('id', $request->id);
            $quiz->delete();
        });

        return redirect()->route('quiz.index')->with('success', 'Record deleted successfully.');
    }

    public function inactivate(Request $request)
    {
        $quiz = Quiz::find($request->id);
        $quiz->is_active = 0;
        $quiz->save();
        return redirect()->route('quiz.index');

    }

    public function activate(Request $request)
    {
        $quiz = Quiz::find($request->id);
        $quiz->is_active = 1;
        $quiz->save();
        return redirect()->route('quiz.index');

    }

    public function private(Request $request)
    {
        $quiz = Quiz::find($request->id);
        $quiz->is_private = $quiz->is_private ? 0 : 1;
        $quiz->update();
        return redirect()->route('quiz.index', ['id' => $request->id]);
    }

    public function createQuestion($id, $quiz_id)
    {
        $question = [];
        $question_index = 0;
        if ($id > 0) {
            $question = Question::with('options')->where('id', $id)->first();
            $question_first = Question::where('quiz_id', $quiz_id)->first();
            $question_index = $question->id - $question_first->id + 1;
        }
        $quiz = Quiz::find($quiz_id);
        return view('backend.quiz.questions.create', [
            'quiz' => $quiz,
            'question' => $question,
            'question_index' => $question_index,
        ]);
    }

    public function saveQuestion($quiz_id, Request $request)
    {
        $this->validate($request, [
            'question' => 'required',
            'options.*' => 'required'
        ]);

        if ($request->id == 0) {
            $question = new Question();
        } else {
            $question = Question::find($request->id);
        }
        $question->quiz_id = $quiz_id;
        $question->question = $request->question;

        if ($request->hasFile('images') && $request->file('images')->isValid()) {
            // Define the path inside the 'public' disk where you want to store the images
            $destinationPath = 'images/questions';

            // Get the uploaded file
            $file = $request->file('images');

            // Generate a unique file name or you can keep the original one
            $fileName = uniqid('question_') . '.' . $file->getClientOriginalExtension();

            // Save the file to the local storage and get the path
            $filePath = $file->storeAs($destinationPath, $fileName, 'public');

            // Update the 'image_url' field in the Question model with the new file path
            $question->image_url = Storage::url($filePath);
        }

        $question->save();

        $quiz = Quiz::find($quiz_id);
        $quiz->questions_count = $quiz->questions_count + 1;
        $quiz->save();

        $existingOptions = $question->options;

        foreach ($request->options as $index => $optionText) {
            // Check if there is an existing option at this index
            if (isset($existingOptions[$index])) {
                // Update the existing option
                $existingOption = $existingOptions[$index];
                $existingOption->option = $optionText;
                $existingOption->is_right_option = ($index == $request->answerOptions) ? 1 : 0;
                $existingOption->update();
            } else {
                // Create a new option if it does not exist
                $newOption = new Option();
                $newOption->question_id = $question->id;
                $newOption->option = $optionText;
                $newOption->is_right_option = ($index == $request->answerOptions) ? 1 : 0;
                $newOption->save();
            }
        }

        // Optionally, remove any extra options if the number of options has decreased
        if (count($existingOptions) > count($request->options)) {
            foreach ($existingOptions as $index => $existingOption) {
                if ($index >= count($request->options)) {
                    $existingOption->delete();
                }
            }
        }
        return redirect()->route('quiz.edit', $quiz_id);
    }

    public function deleteQuestion(Request $request)
    {
        $question = Question::find($request->id); // Find the question, or fail with a 404 error

        $question->options()->delete(); // This deletes all related options

        $question->delete();

        return redirect()->route('quiz.edit', $request->quizID);
    }

    public function updateImage(Request $request, $id)
    {
        $quiz = Quiz::find($id);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('public');
            $fileName = basename($path);
            $quiz->image_url = $fileName;
            $quiz->save();
            return response()->json(['success' => 'Image updated successfully', 'image_url' => $quiz->image_url]);
        }

        return response()->json(['error' => 'No image uploaded']);
    }
}
