<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'age' => 'required',
            'password' => 'required|confirmed',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $path = null;

        if ($request->hasFile('urlAvatar')) {
            $file = $request->file('urlAvatar');

            // You can also validate the file here (e.g., size, mime types)

            // Generate a unique file name to prevent overwriting
            $fileName = time().'.'.$file->getClientOriginalExtension();

            // Save the file to your desired location, 'public' could be any disk defined in your filesystems.php config
            $path = $file->storeAs('images', $fileName, 'public');

            // If you want to save the path to the database, you can use $path variable

            // Return success response or redirect
        }

        $user = User::create([
            'urlAvatar' => $path,
            'name' => $request->name,
            'email' => $request->email,
            'age' => $request->age,
            'aboutMe' => null,
            'password' => bcrypt($request->password)
        ]);

        $my_role = Role::where('id', '=', 3)->firstOrFail();

        $user->assignRole($my_role); //Assigning role to user

        $success['token'] =  $user->createToken('MedQuiz')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 200);
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MedQuiz')->plainTextToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 200);
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
}
