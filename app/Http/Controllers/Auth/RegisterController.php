<?php

namespace App\Http\Controllers\Auth;



use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
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
            'name' => ['required', 'string', 'min:3,max:255'],
            'last_name' => ['required', 'string', 'min:3,max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'path_picture' => ['required', 'image'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

            
          $upload_dir_name = "/public/Pictures/";
          $pathx = $upload_dir_name . basename($_FILES['path_picture']['name']);
        
          move_uploaded_file(basename($_FILES['path_picture']['tmp_name']), $_SERVER['DOCUMENT_ROOT'] . $upload_dir_name . basename($data['path_picture']));
          
            return User::create([
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'path_picture' => $pathx,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
    }
}
