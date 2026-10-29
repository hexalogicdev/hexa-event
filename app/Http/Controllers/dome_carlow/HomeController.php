<?php

namespace App\Http\Controllers\dome_carlow;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;


class HomeController extends Controller
{
  public function index(Request $request)
  {
    $search = trim((string) $request->input('q'));
    $location = trim((string) $request->input('location'));
    $date = $request->filled('date')
      ? $request->validate(['date' => ['date']])['date']
      : null;

    $events = Event::with(['ticketTypes' => fn ($query) => $query->where('is_active', true)])
      ->withCount('attendees')
      ->publiclyRegistrable()
      ->where('event_end_date', '>=', now())
      ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
        $query->where('name', 'like', "%{$search}%")
          ->orWhere('description', 'like', "%{$search}%")
          ->orWhere('location', 'like', "%{$search}%");
      }))
      ->when($location !== '', fn ($query) => $query->where('location', 'like', "%{$location}%"))
      ->when($date, fn ($query) => $query->whereDate('event_start_date', $date))
      ->orderBy('event_start_date')
      ->take(12)
      ->get();

    return view('index', compact('events'));
  }



  public function date_time()
  {
    return view('dome_carlow.date_time');
  }
  public function personal_info()
  {
    return view('dome_carlow.personal_info');
  }
}
