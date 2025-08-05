<?php

namespace App\Http\Controllers\dome_carlow;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;


class HomeController extends Controller
{
  public function index(Request $request)
  {
    return redirect()->route('login');

    return view('index');
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
