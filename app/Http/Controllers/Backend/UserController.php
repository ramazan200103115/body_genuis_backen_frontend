<?php

namespace App\Http\Controllers\Backend;

use App\Models\Quiz;
use App\Models\User;
use App\Models\UserParticipant;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function index()
    {

        $users = User::orderBy('updated_at', 'desc')->paginate(20);

        return view('backend.users.index')->with('users', $users);
    }

    public function getUsers(Request $request)
    {
        $users = User::role('User')->orderBy('updated_at', 'desc')->paginate(20);
        if (!empty($request->id)) {
            $selectedUser = $users->where('id', $request->id)->first(); // As an example, select the first user
        } else {
            $selectedUser = $users->first(); // As an example, select the first user
        }
        $selectedUser->average_score = (int)UserParticipant::where('user_id', $selectedUser->id)->avg('score');
        $selectedUser->passed_quizzes = (int)UserParticipant::where('user_id', $selectedUser->id)->count();
        return view('backend.users.indexUsers')->with([
            'users' => $users,
            'selectedUser' => $selectedUser
        ]);
    }

    public function getTeachers(Request $request)
    {
        $users = User::role('Teacher')->orderBy('updated_at', 'desc')->paginate(20);
        if (!empty($request->id)) {
            $selectedUser = $users->where('id', $request->id)->first(); // As an example, select the first user
        } else {
            $selectedUser = $users->first(); // As an example, select the first user
        }

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
        $QuizzesCount = Quiz::where('author', $selectedUser->id)->count();

        $selectedUser->totalPassedStudents = (int)$totalPassedStudents;
        $selectedUser->averageScores = (int)$overallAverageScore;
        $selectedUser->publicQuizzesCount = (int)$publicQuizzesCount;
        $selectedUser->QuizzesCount = (int)$QuizzesCount;

        return view('backend.users.indexTeachers')->with([
            'users' => $users,
            'selectedUser' => $selectedUser
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::get();

        return view('backend.users.create', ['roles' => $roles]);
    }

    public function createTeacher()
    {
        return view('backend.users.createTeacher');
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
            'name' => 'required|max:120',
            'email' => 'required|email|unique:users',
            'age' => 'required',
            'password' => 'required|min:5|confirmed'
        ]);

        $path = null;

        if ($request->hasFile('urlAvatar')) {
            $file = $request->file('urlAvatar');

            // You can also validate the file here (e.g., size, mime types)

            // Generate a unique file name to prevent overwriting
            $fileName = time() . '.' . $file->getClientOriginalExtension();

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
            'aboutMe' => $request->aboutMe,
            'password' => bcrypt($request->password)
        ]);

        $role = $request->role;
        $my_role = Role::where('id', '=', $role)->firstOrFail();

        $user->assignRole($my_role); //Assigning role to user

        //  flash('User successfully added!')->success();
        return redirect()->route('user.getUsers');
    }

    public function storeTeacher(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:120',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $my_role = Role::where('id', '=', 2)->firstOrFail();

        $user->assignRole($my_role); //Assigning role to user

        //  flash('User successfully added!')->success();
        return redirect()->route('user.getTeachers');
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
    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id); //Get user with specified id
        $roles = Role::get(); //Get all roles
        $permissions = Permission::all();

        return view('backend.users.edit', compact('user', 'roles', 'permissions')); //pass user and roles data to view
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        //Validate name, email and password fields
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'admin_password' => 'required'
        ]);

        //check admin password
        if (!\Hash::check($request->admin_password, Auth::user()->password)) {

            return redirect()->back()->with('error', 'Wrong admin password.');
        }

        $user = User::findOrFail($id); //Get role specified by id

        if ($request->password) {
            $user->email = $request->email;
            $user->name = $request->name;
            $user->password = bcrypt($request->password);
        } else {
            $user->email = $request->email;
            $user->name = $request->name;
        }

        $user->save();

        $roles = $request['roles']; //Retreive all roles

        if (isset($roles)) {
            $user->roles()->sync($roles);  //If one or more role is selected associate user to roles
        } else {
            $user->roles()->detach(); //If no role is selected remove exisiting role associated to a user
        }

        return redirect()->route('user.index')->with('success', 'User successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->blocked = $user->blocked ? 0 : 1;
        $user->save();

        if ($user->hasRole('User')) {
            return redirect()->route('user.getUsers')->with('success', 'User successfully deleted.');
        } elseif ($user->hasRole('Teacher')) {
            return redirect()->route('user.getTeachers')->with('success', 'User successfully deleted.');
        }
    }


    /**
     * update user permissions
     *
     * @param mixed $request
     * @param mixed $id
     * @return void
     */
    public function updatePermission(Request $request, $id)
    {

        $user = User::findOrFail($id);

        //Validate name, email and password fields
        $this->validate($request, [
            'admin_password' => 'required'
        ]);

        if (!\Hash::check($request->admin_password, Auth::user()->password)) {

            return redirect()->back()->with('error', 'Wrong admin password.');
        }

        $permissions = $request->permissions;

        $user->revokePermissionTo($permissions);    //revoke all previous permissions
        $user->givePermissionTo($request->permissions); //assign permissions

        return redirect()->route('user.index')->with('success', 'Permissions updated successfully.');
    }
}
