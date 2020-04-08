<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Filmvieweds;
use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Validator;

class FilmviewedController extends Controller
{
  public function movie_viewed(Request $request)
  {
    $x = Filmvieweds::where('user_id', auth()->id())->where('name', $request->title)->get();
    if ($x != '[]')
      return;
    else
    {
      $film = new FilmVieweds;
      $film->user_id = $request->id_user;
      $film->id_movie = $request->id_movie;
      $film->name = $request->title;
      $film->hash = $request->hash;
      $film->save();
      return;
      }
  }
}
