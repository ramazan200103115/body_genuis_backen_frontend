<?php

use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\EducationController;
use App\Http\Controllers\Backend\QuizController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SearchController;
use App\Http\Controllers\Backend\SearchUserController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::controller(QuizController::class)->group(function () {
    Route::get('quiz', 'index')->name('quiz');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', function () {
        return redirect('/quiz');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('search/user', [SearchUserController::class, 'search'])->name('search-user');
    Route::get('search/education', [SearchController::class, 'searchEducation'])->name('search-education');
    Route::get('search/quiz', [SearchController::class, 'searchQuiz'])->name('search-quiz');
    Route::post('user-permission-update/{id}', [UserController::class, 'updatePermission]'])->name('user-permission-update');

    Route::resource('user', UserController::class);

    Route::post('/import-education', [EducationController::class, 'import'])->name('import-education');

    Route::controller(EducationController::class)->group(function () {
        Route::post('/delete-image', 'deleteImage');
        Route::post('/delete-question-image/{id}', 'deleteQuestionImage');
        Route::get('/education', [EducationController::class, 'index'])->name('education.index');
        Route::get('/education/create', [EducationController::class, 'create'])->name('education.create');
        Route::post('/education', [EducationController::class, 'store'])->name('education.store');
        Route::get('/education/{education}', [EducationController::class, 'show'])->name('education.show');
        Route::get('/education/edit/{id}', [EducationController::class, 'edit'])->name('education.edit');
        Route::put('/education/{education}', [EducationController::class, 'update'])->name('education.update');
        Route::delete('/education/delete', [EducationController::class, 'destroy'])->name('education.destroy');

        Route::get('education/{id}/info', 'info')->name('education.info');
        Route::post('/info', 'saveInfo')->name('education.saveInfo');

        Route::get('education/{id}/diseases', 'diseases')->name('education.diseases');
        Route::post('/diseases', 'saveDiseases')->name('education.saveDiseases');
        Route::get('education/question/{id}/{education_id}', 'createQuestion')->name('education.create.question');
        Route::delete('education/question/delete', 'deleteQuestion')->name('education.delete.question');
        Route::post('education/question/{education_id}', 'saveQuestion')->name('education.save.question');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('getUsers', 'getUsers')->name('user.getUsers');
        Route::get('getTeachers', 'getTeachers')->name('user.getTeachers');
        Route::get('createTeacher', 'createTeacher')->name('teacher.create');
        Route::post('store', 'storeTeacher')->name('teacher.store');
    });
    Route::resource('role', RoleController::class);

    Route::controller(QuizController::class)->group(function () {
        Route::get('quiz', 'index')->name('quiz.index');
        Route::get('quiz/questions', 'questions')->name('quiz.questions');
        Route::post('quiz', 'store')->name('quiz.store');
        Route::post('activate', 'activate')->name('quiz.activate');
        Route::post('inactivate', 'inactivate')->name('quiz.inactivate');
        Route::post('private', 'private')->name('quiz.private');
        Route::get('/quiz/create', 'create')->name('quiz.create');
        Route::get('quiz/edit/{id}', 'edit')->name('quiz.edit');
        Route::post('quiz/edit/{id}', 'update');
        Route::get('quiz/question/{id}/{quiz_id}', 'createQuestion')->name('create.question');
        Route::delete('quiz/question/delete', 'deleteQuestion')->name('delete.question');
        Route::post('quiz/question/{quiz_id}', 'saveQuestion')->name('save.question');
        Route::delete('quiz/delete', 'destroy')->name('quiz.destroy');
        Route::post('/quiz/update-image/{id}', 'updateImage')->name('quiz.updateImage');

    });
    Route::post('quiz/invite/{slug}', [\App\Http\Controllers\Backend\QuizShareController::class, 'invite'])->name('quiz.invite');

});
