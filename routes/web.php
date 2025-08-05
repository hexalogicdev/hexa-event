<?php


use App\Http\Controllers\AddOnController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClassSessionController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\dome_carlow\HomeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\File;

Auth::routes();

Route::group(['middleware' => 'auth'], function () {

  Route::resource('User', UserController::class);
  Route::resource('student', StudentController::class);
  Route::resource('course', CourseController::class);
  Route::resource('session', ClassSessionController::class);
  Route::get('changeStatus/{model}/{id}', [UserController::class, 'changeStatus'])->name('changeStatus');
  Route::get('dashboard', [UserController::class, 'dashboard'])->name('dashboard');
  Route::get('parents', [UserController::class, 'parents'])->name('parents');
});
// Route::get('thankYou', [BookingController::class, 'thankYou'])->name('thankYou');


// Route::view('/', 'home');
Route::view('/about', 'about');
Route::view('/packages', 'packages');
Route::view('/contact', 'contact');

// Main Page Route
Route::get('/', [HomeController::class, 'index'])->name('dome-carlow.index');

// Function to provide the laravel.log file for download
Route::get('/logsDownload', function () {
  $logFile = storage_path('logs/laravel.log');

  if (File::exists($logFile)) {
    return response()->download($logFile, 'laravel.log');
  }

  return response()->json(['message' => 'Log file does not exist.'], 404);
});
