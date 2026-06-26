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
  Route::get('profile', [UserController::class, 'profile'])->name('profile.show');
  Route::post('profile', [UserController::class, 'updateProfile'])->name('profile.update');
  Route::resource('events', EventController::class);
  Route::get('events/{event}/fields', [EventController::class, 'fields'])->name('events.fields');
  Route::post('events/{event}/fields', [EventController::class, 'updateFields'])->name('events.fields.update');
  Route::get('events/{event}/attendees', [EventController::class, 'attendees'])->name('events.attendees');
  Route::get('events/{event}/report', [EventController::class, 'report'])->name('events.report');
  Route::get('events/{event}/attendees/export', [EventController::class, 'export'])->name('events.attendees.export');
  Route::post('attendees/{attendee}/check-in', [AttendeeController::class, 'manualCheckIn'])->name('attendees.manual-check-in');
  Route::get('changeStatus/{model}/{id}', [UserController::class, 'changeStatus'])->name('changeStatus');
  Route::get('dashboard', [UserController::class, 'dashboard'])->name('dashboard');
});
Route::resource('attendee', AttendeeController::class);
Route::get('/events/register/{event_code}', [EventController::class, 'registrationForm'])->name('events.registration');
Route::view('/legal/privacy-terms', 'profile.policy')->name('legal.policy');
Route::get('thankYou/{id}', [CheckInController::class, 'thankYou'])->name('thankYou');
Route::get('/check-in', [CheckInController::class, 'scan'])->name('attendee.qr.scan');
Route::post('/check-in', [CheckInController::class, 'verifyScan'])->name('attendee.qr.scan.verify');
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
