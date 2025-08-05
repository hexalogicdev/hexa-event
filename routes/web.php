<?php


use App\Http\Controllers\AttendeeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dome_carlow\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\checkInController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\File;

Auth::routes();

Route::group(['middleware' => 'auth'], function () {

  Route::resource('User', UserController::class);
  Route::resource('events', EventController::class);
  Route::get('changeStatus/{model}/{id}', [UserController::class, 'changeStatus'])->name('changeStatus');
  Route::get('dashboard', [UserController::class, 'dashboard'])->name('dashboard');
});
Route::resource('attendee', AttendeeController::class);
Route::get('/events/register/{event_code}', [EventController::class, 'registrationForm'])->name('events.registration');
Route::get('thankYou/{id}', [CheckInController::class, 'thankYou'])->name('thankYou');
Route::get('/check-in/{qr_id}', [CheckInController::class, 'show'])->name('attendee.qr');
Route::post('/check-in/{qr_id}', [CheckInController::class, 'verifyPassword'])->name('attendee.qr.verify');

// Main Page Route
Route::get('/', [HomeController::class, 'index']);

// Function to provide the laravel.log file for download
Route::get('/logsDownload', function () {
  $logFile = storage_path('logs/laravel.log');

  if (File::exists($logFile)) {
    return response()->download($logFile, 'laravel.log');
  }

  return response()->json(['message' => 'Log file does not exist.'], 404);
});
