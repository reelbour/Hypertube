<?php

namespace App\Http\Controllers;
use App\User;

use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show_user(Request $id)
    {
      $id = $id->path();

      $id = substr($id, 12);
      $users = User::where('id', $id)->first();

      if (!(isset($users)))
        return view('home');
      //dd($users);

      //$users = new user($id);
      return view('UserProfile', compact('users'));
    }

}
