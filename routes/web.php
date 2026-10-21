<?php


use App\Http\Controllers\AttendeeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dome_carlow\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SecurityOfficerController;

// Accounts are provisioned by the platform administrator. Keep the existing
// registration controller/views in place, but do not expose public sign-up.
Auth::routes(['register' => false]);

Route::group(['middleware' => ['auth', 'active']], function () {

  Route::get('dashboard', [UserController::class, 'dashboard'])->name('dashboard');

  Route::middleware('role:superadmin,organizer')->group(function () {
    Route::get('profile', [UserController::class, 'profile'])->name('profile.show');
    Route::post('profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::resource('events', EventController::class);
    Route::get('events/{event}/fields', [EventController::class, 'fields'])->name('events.fields');
    Route::post('events/{event}/fields', [EventController::class, 'updateFields'])->name('events.fields.update');
    Route::get('events/{event}/attendees', [EventController::class, 'attendees'])->name('events.attendees');
    Route::get('events/{event}/report', [EventController::class, 'report'])->name('events.report');
    Route::get('events/{event}/attendees/export', [EventController::class, 'export'])->name('events.attendees.export');
    Route::post('attendees/{attendee}/check-in', [AttendeeController::class, 'manualCheckIn'])->name('attendees.manual-check-in');
    Route::get('security-officers', [SecurityOfficerController::class, 'index'])->name('security-officers.index');
    Route::post('security-officers', [SecurityOfficerController::class, 'store'])->name('security-officers.store');
    Route::delete('security-officers/{securityOfficer}', [SecurityOfficerController::class, 'destroy'])->name('security-officers.destroy');
    Route::patch('security-officers/{securityOfficer}/status', [SecurityOfficerController::class, 'updateStatus'])->name('security-officers.status');
    Route::get('events/{event}/security-officers', [SecurityOfficerController::class, 'assignments'])->name('events.security-officers');
    Route::put('events/{event}/security-officers', [SecurityOfficerController::class, 'updateAssignments'])->name('events.security-officers.update');
  });

  Route::middleware('role:superadmin')->group(function () {
    Route::resource('User', UserController::class);
    Route::get('changeStatus/{model}/{id}', [UserController::class, 'changeStatus'])->name('changeStatus');
  });

  Route::get('/check-in', [CheckInController::class, 'scan'])->name('attendee.qr.scan');
  Route::post('/check-in', [CheckInController::class, 'verifyScan'])->name('attendee.qr.scan.verify');
  Route::get('/check-in/{qr_id}', [CheckInController::class, 'show'])->name('attendee.qr');
  Route::post('/check-in/{qr_id}', [CheckInController::class, 'verifyPassword'])->name('attendee.qr.verify');
});
Route::post('attendee', [AttendeeController::class, 'store'])->name('attendee.store');
Route::get('/events/register/{event_code}', [EventController::class, 'registrationForm'])->name('events.registration');
Route::view('/legal/privacy-terms', 'profile.policy')->name('legal.policy');
Route::get('thankYou/{id}', [CheckInController::class, 'thankYou'])->middleware('signed')->name('thankYou');

// Main Page Route
Route::get('/', [HomeController::class, 'index']);
