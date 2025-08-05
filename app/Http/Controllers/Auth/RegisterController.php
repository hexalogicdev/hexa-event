<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;

class RegisterController extends Controller
{
  /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

  use RegistersUsers;

  /**
   * Where to redirect users after registration.
   *
   * @var string
   */
  protected $redirectTo = '/dashboard';

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('guest');
  }
  public function showRegistrationForm()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('auth.register', ['pageConfigs' => $pageConfigs]);
  }
  /**
   * Get a validator for an incoming registration request.
   *
   * @param  array  $data
   * @return \Illuminate\Contracts\Validation\Validator
   */
  protected function validator(array $data)
  {
    return Validator::make($data, [
      'username' => ['required', 'string', 'min:3', 'max:50'],
      'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
      'phone' => ['required', 'regex:/^\+?[0-9]{10,15}$/'],
      'address' => ['required', 'string', 'min:5', 'max:100'],
      'country' => ['required', 'string', 'in:USA,UK,Canada,Australia,Ireland,Pakistan'],
      'gender' => ['required', 'in:Male,Female'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);
  }

  /**
   * Create a new user instance after a valid registration.
   *
   * @param  array  $data
   * @return \App\Models\User
   */
  protected function create(array $data)
  {
    $role = Role::where('name', 'parent')->first();
    return User::create([
      'name' => $data['username'],
      'email' => $data['email'],
      'password' => Hash::make($data['password']),
      'user_type' => 'parent',
      'role_id' => $role->id,
      'gender' => $data['gender'],
      'country' => $data['country'],
      'address' => $data['address'],
      'phone' => $data['phone'],
    ]);
  }
}
