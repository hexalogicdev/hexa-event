<?php

namespace App\Http\Controllers\dome_carlow;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;


class HomeController extends Controller
{
  public function index(Request $request)
  {
    $events = Event::with(['ticketTypes' => fn ($query) => $query->where('is_active', true)])
      ->withCount('attendees')
      ->where('status', 'published')
      ->where('visibility', 'public')
      ->where('is_active', true)
      ->where('event_end_date', '>=', now())
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
