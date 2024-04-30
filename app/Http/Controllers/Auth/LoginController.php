<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    public const HOME = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            $user = Auth::user();

            // Check user role and redirect accordingly
            if ($user->hasRole('Super Admin')) { // Assuming you have a method to check user roles
                return redirect()->intended(self::HOME);
            } elseif ($user->hasRole('Teacher')) { // Assuming you have a method to check user roles
                return redirect()->route('quiz.index');
            } else{
                return redirect()->intended('login');
            }
        }
        return redirect()->route('login');

    }
}
