<?php


use App\Http\Controllers\AttendeeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dome_carlow\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SecurityOfficerController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationInvitationController;
use App\Http\Controllers\OrganizationMemberController;
use App\Http\Controllers\OrganizationEventAllowanceController;
use App\Http\Controllers\EventAllowanceRequestController;

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
    Route::post('organizations', [OrganizationController::class, 'store'])->name('organizations.store');
    Route::patch('organizations/{organization}/status', [OrganizationController::class, 'updateStatus'])->name('organizations.status');
    Route::post('organizations/{organization}/event-allowance-adjustments', [OrganizationEventAllowanceController::class, 'store'])->name('organizations.event-allowance-adjustments.store');
    Route::get('event-allowance-requests', [EventAllowanceRequestController::class, 'index'])->name('event-allowance-requests.index');
    Route::patch('event-allowance-requests/{allowanceRequest}', [EventAllowanceRequestController::class, 'review'])->name('event-allowance-requests.review');
  });

  Route::get('organizations', [OrganizationController::class, 'index'])->name('organizations.index');
  Route::get('organizations/{organization}', [OrganizationController::class, 'show'])->name('organizations.show');
  Route::post('organizations/{organization}/members', [OrganizationMemberController::class, 'store'])->name('organizations.members.store');
  Route::post('organizations/{organization}/event-allowance-requests', [EventAllowanceRequestController::class, 'store'])->name('organizations.event-allowance-requests.store');
  Route::patch('event-allowance-requests/{allowanceRequest}/cancel', [EventAllowanceRequestController::class, 'cancel'])->name('event-allowance-requests.cancel');
  Route::post('organizations/{organization}/event-allowance-requests', [EventAllowanceRequestController::class, 'store'])->name('organizations.event-allowance-requests.store');
  Route::patch('event-allowance-requests/{allowanceRequest}/cancel', [EventAllowanceRequestController::class, 'cancel'])->name('event-allowance-requests.cancel');

  Route::get('/check-in', [CheckInController::class, 'scan'])->name('attendee.qr.scan');
  Route::post('/check-in', [CheckInController::class, 'verifyScan'])->name('attendee.qr.scan.verify');
  Route::get('/check-in/{qr_id}', [CheckInController::class, 'show'])->name('attendee.qr');
  Route::post('/check-in/{qr_id}', [CheckInController::class, 'verifyPassword'])->name('attendee.qr.verify');
});
Route::middleware('throttle:20,1')->group(function () {
  Route::get('organization-invitations/{token}', [OrganizationInvitationController::class, 'show'])->name('organization-invitations.show');
  Route::post('organization-invitations/{token}', [OrganizationInvitationController::class, 'accept'])->name('organization-invitations.accept');
});
Route::post('attendee', [AttendeeController::class, 'store'])->name('attendee.store');
Route::get('/events/register/{event_code}', [EventController::class, 'registrationForm'])->name('events.registration');
Route::view('/legal/privacy-policy', 'profile.policy', ['policy' => 'privacy'])->name('legal.privacy');
Route::view('/legal/terms', 'profile.policy', ['policy' => 'terms'])->name('legal.terms');
Route::view('/legal/refund-policy', 'profile.policy', ['policy' => 'refund'])->name('legal.refund');
Route::redirect('/legal/privacy-terms', '/legal/privacy-policy', 301)->name('legal.policy');
Route::get('thankYou/{id}', [CheckInController::class, 'thankYou'])->middleware('signed')->name('thankYou');

// Main Page Route
Route::get('/', [HomeController::class, 'index']);
