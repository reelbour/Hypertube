<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Comment;
use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Validator;



class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


      // verify that te comment isnt empty and is valid here
      //here return back with info (comm have been added INsuccefuly)

      $validator = Validator::make($request->all(), [
        'content' => 'required|string|min:1,max:500'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }


      $comment = new comment;
      $comment->user_id = auth()->id();

      $comment->name = Auth()->user()->name;
      $comment->content_id = $request->id;
      $comment->content = $request->content;
      $comment->save();

      //here return back with info (comm have been added succefuly)

      return back();

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comment = comment::where('content_id' , $id)->orderBy('created_at', 'desc')->get();
        return $comment;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

}
