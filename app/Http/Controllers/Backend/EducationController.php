<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Models\EducationImages;
use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

class EducationController extends Controller
{
    public function __construct(
        Option   $option,
        Question $question
    )
    {
        $this->option = $option;
        $this->question = $question;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->session()->forget('images_saved'); // Forget this session variable when an image is deleted
        $educations = Education::select('id', 'title', 'created_at')
            ->withCount('questions') // This adds a `questions_count` attribute to each Education model
            ->get();
        if (!empty($request->id)) {
            $selectedEducation = $educations->where('id', $request->id)->first(); // As an example, select the first user
        } else {
            $selectedEducation = $educations->first(); // As an example, select the first user
        }
        return view('backend.education.index')->with([
            'educations' => $educations,
            'selectedEducation' => $selectedEducation
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.education.create');
    }

    /*

    public function store(Request $request)
    {
        //dd($request);
        if (!empty($request->id)) {
            $education = Education::find($request->id);
            if (!empty($education)) {
                $is_info = 1;
                if (!empty($request->info)) {
                    $education->info = $request->info;
                }
                if (!empty($request->diseases)) {
                    $education->diseases = $request->diseases;
                    $is_info = 0;
                }
                $maxUrlsPerEducation = 10;

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        if (EducationImages::where('education_id', $education->id)->count() >= $maxUrlsPerEducation) {
                            // Maximum limit reached, handle accordingly (e.g., display an error message)
                            return response()->json(['error' => 'Maximum limit of URLs reached for this education.'], 422);
                        } else {
                            // Store the uploaded image in storage/app/public directory
                            $path = $image->store('public');
                            $fileName = basename($path);

                            $educationImages = EducationImages::create([
                                'education_id' => $education->id,
                                'url' => $fileName,
                                'is_info' => $is_info
                            ]);
                        }
                    }
                }
                $education->save();
            }
        } else {
            $this->validate($request, [
                'name' => 'required|max:120',
            ]);

            $education = Education::create([
                'name' => $request->name
            ]);
        }

        return redirect()->route('education.index');

    }
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!empty($request->id)) {
            $education = Education::find($request->id);
            if (!empty($education)) {
                $is_info = 1;
                if (!empty($request->info)) {
                    $education->info = $request->info;
                }
                if (!empty($request->diseases)) {
                    $education->diseases = $request->diseases;
                    $is_info = 0;
                }
            }
            if ($request->hasFile('images')) {
                $this->processImages($request, $education);
            }

            $education->save();
            return response()->json([
                'success' => true,
                'redirectUrl' => action([EducationController::Class, 'index'])
            ]);
        } else {
            $this->validate($request, [
                'title' => 'required|max:120',
            ]);

            $education = Education::create([
                'title' => $request->title
            ]);
        }
        return redirect()->action([EducationController::Class, 'index']);
    }

    private function processImages(Request $request, $education)
    {
        $maxUrlsPerEducation = 10;
        $existingCountInfo = EducationImages::where('education_id', $education->id)->where('is_info', 1)->count();
        $existingCountDieases = EducationImages::where('education_id', $education->id)->where('is_info', 0)->count();

        foreach ($request->file('images') as $image) {
            if ($request->has('info') && $existingCountInfo <= $maxUrlsPerEducation) {
                $path = $image->store('public');
                $fileName = basename($path);

                EducationImages::create([
                    'education_id' => $education->id,
                    'url' => $fileName,
                    'is_info' => 1
                ]);
                $existingCountInfo++;
            }
            if (!$request->has('info') && $existingCountDieases <= $maxUrlsPerEducation) {
                $path = $image->store('public');
                $fileName = basename($path);

                EducationImages::create([
                    'education_id' => $education->id,
                    'url' => $fileName,
                    'is_info' => 0
                ]);
                $existingCountDieases++;
            }
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $education = Education::where('id', $id)->firstOrFail();

        $questions = $this->question->with('options')
            ->where('education_id', $education->id)->get();

        return view('backend.education.edit', [
            'education' => $education,
            'questions' => $questions,
            'type' => 'choice'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $education = Education::find($request->id);
        if ($education) {
            if (!empty($education->images())){
                $education->images()->delete();
            }
            if (!empty($education->questions())){
                $education->questions()->delete();
            }
            $education->delete(); // This triggers the deleting event
        }
        return redirect()->route('education.index')->with('message', 'Education deleted successfully.');

    }

    public function deleteImage(Request $request)
    {
        if ($request->type === 'info') {
            $image = EducationImages::find($request->imageId);
            if ($image) {
                Storage::delete('public/' . $image->url);
                if ($image->delete()) {
                    return response()->json(['message' => 'Image deleted successfully'], 200);

                }
            }
        } elseif ($request->type === 'diseases') {
            $image = EducationImages::find($request->imageId);
            if ($image) {
                Storage::delete('public/' . $image->url);
                $image->delete();
            }
            return response()->json(['message' => 'Image deleted successfully'], 200);
        }
        return response()->json(['error' => 'Image not found'], 404);

    }

    public function deleteQuestionImage($id)
    {
        $question = Question::find($id);
        $question->image_url = null;
        $question->save();

        return response()->json(['message' => 'Image deleted successfully'], 200);
    }

    private function destroyImage($image)
    {
        // Optional: Delete the file from storage if necessary

    }

    public function info($id)
    {
        $info = Education::select('id', 'title', 'info')->where('id', $id)->first();
        $info_images = EducationImages::select('id', 'url')->where('education_id', $info->id)->where('is_info', 1)->get();
        return view('backend.education.info')->with([
            'info' => $info,
            'info_images' => $info_images,
        ]);
    }

    public function diseases($id)
    {
        $diseases = Education::select('id', 'title', 'diseases')->where('id', $id)->first();
        $diseases_images = EducationImages::select('id', 'url')->where('education_id', $diseases->id)->where('is_info', 0)->get();

        return view('backend.education.diseases')->with([
            'diseases' => $diseases,
            'diseases_images' => $diseases_images,
        ]);
    }

    public function import(Request $request)
    {
        // Get the uploaded file
        $xmlFile = $request->file('xml_file');

        // Load the XML content
        $xmlContent = file_get_contents($xmlFile->path());
        $educations = new SimpleXMLElement($xmlContent);

        // Iterate through each <education> element
        foreach ($educations->education as $education) {
            // Create a new Education model instance
            $newEducation = new Education();

            // Assign values from XML to model properties
            $newEducation->title = (string)$education->name;
            $newEducation->info = (string)$education->info;
            $newEducation->diseases = (string)$education->diseases;

            // Save the model to database
            $newEducation->save();
        }

        return redirect()->route('education.index');
    }

    public function createQuestion($id, $education_id)
    {
        $question = [];
        $question_index = 0;
        if ($id > 0) {
            $question = Question::with('options')->where('id', $id)->first();
            $question_first = Question::where('education_id', $education_id)->first();
            $question_index = $question->id - $question_first->id + 1;
        }
        $education = Education::find($education_id);
        return view('backend.education.questions.create', [
            'education' => $education,
            'question' => $question,
            'question_index' => $question_index,
        ]);
    }

    public function saveQuestion($education_id, Request $request)
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
        $question->education_id = $education_id;
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
        return redirect()->route('education.edit', $education_id);
    }
}
