<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AjaxController extends Controller
{
    //
    public function ajax_call(){

	\Log::info("ajax_call()");
	$nb_users = \DB::table('users')->count();
	$nb_users = json_encode($nb_users);
  //return $nb_users;
  // $array = array('sosa', '1234', 'path_img', 'ksosjsjsosj');
   $comment = app('App\Http\Controllers\CommentController')->show('8462');
   //($comment);
   $comment = compact($comment);
  // $comment = json_encode($comment);
	 return $comment;
    }
}
