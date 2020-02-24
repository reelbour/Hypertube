<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;


class MyprofileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $users= User::Auth->user('id')->get;

        // $users = Auth::user();

        $users = auth()->user();
        return view('index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = auth()->user();
        return view('edit', compact('users'));
    }
    
    


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        //update user info here
        $users = auth()->user();
         $img = $users->image;
         $id = $users->id;

        
        if (isset($request->image))
        {
            //si ca existe upload le fichier sinon juste remetre le path de l ancien
            //$request->image = $img;
        }
        
      
            $psw = Hash::make($request->password);
       
        

        
    // echo $request;

     $lastname = $request->last_name;
        $email = $users->email;

        echo $email;
      
      $users->where('id', $id)->update(['name' => $request->name, 'last_name' => $lastname, 'email' => $email, 'image' => $img, 'password' => $psw]);
      // $users->update($request->all());
        $users->update($request->has('image') ? $request->all() : $request->except(['image']));


      
      return redirect()->route('myprofile.index')->with('info', 'Your account have been updated !');
       
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }
}
