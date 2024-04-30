<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Models\EducationImages;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function index()
    {
        $educations = Education::select('id', 'title')->get();
        return $this->sendResponse($educations, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Fetch the education details based on the provided ID
        $education = Education::select('id', 'title', 'info', 'diseases')
            ->where('id', $id)
            ->first();

        // Define the base URL for your images
        // This could be something like 'https://example.com/'
        $baseUrl = config('app.url') . '/storage'; // Assuming you have this configured in your app's config or .env
        $baseUrl = url('storage/'); // Assuming you have this configured in your app's config or .env

        // If the education detail exists
        if ($education) {
            // Retrieve and format the info images URLs as an array of strings
            $education->infoImages = EducationImages::where('education_id', $id)
                ->where('is_info', 1)
                ->pluck('url')
                ->map(function ($url) use ($baseUrl) {
                    // Prepend the base URL to each image URL
                    return $baseUrl . '/' . ltrim($url, '/');
                })
                ->toArray();

            // Retrieve and format the diseases images URLs as an array of strings
            $education->diseasesImages = EducationImages::where('education_id', $id)
                ->where('is_info', 0)
                ->pluck('url')
                ->map(function ($url) use ($baseUrl) {
                    // Prepend the base URL to each image URL
                    return $baseUrl . '/' . ltrim($url, '/');
                })
                ->toArray();

            // Return the formatted response
            return $this->sendResponse($education, 200);
        }

        // Handle the case where the education detail is not found
        return $this->sendError('Education not found.', 404);
    }


}
