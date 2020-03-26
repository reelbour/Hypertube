<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentsController extends Controller
{

    public function return()
    {
      // echo 'sosa';
      print_r($_POST);
      //return back();
    }
    public function add()
    {
      print_r($_POST);
    }
}
