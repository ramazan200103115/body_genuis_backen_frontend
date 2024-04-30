<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function profile()
    {
        $id = auth()->id();
        $user = User::select(
            'id',
            'urlAvatar',
            'name',
            'email',
            'age',
            'medcoins',
            'aboutMe',
            'blocked'
        )->where('id',$id)->first();
        //$user = User::find($id);

        if (!$user) {
            return $this->sendError('User not found',401);
        }
        if ($user->blocked == 1){
            return $this->sendError('User blocked',401);
        }
         return response()->json($user, 200);
    }

    public function medcoins(Request $request)
    {
        if (!empty($request->user_id) && !empty($request->coins)){
            $user = User::find($request->user_id);
            $user->medcoins = $request->coins;
            $user->save();
            return $this->sendResponse('User coins updated',200);
        }else{
            return $this->sendError('User id and coins required',403);
        }
    }

    public function update($id,Request $request)
    {
        // Set validation rules to 'sometimes' to only validate when fields are present
        $rules = [
            'urlAvatar' => 'sometimes',
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,',
            'age' => 'sometimes|integer',
            'medcoins' => 'sometimes|numeric',
            'aboutMe' => 'sometimes|string|max:1000',
            'password' => 'sometimes|string|min:6',
            'status' => 'sometimes|string|max:255'
        ];

        // Validate the request data
        $data = $request->validate($rules);

        // If a new password is provided, hash it
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user = User::find($id);
        // Update the user with only the data provided
        $user->update(array_filter($data));

        // Redirect with success message
        return $this->sendResponse('User profile updated',200);
    }
}
