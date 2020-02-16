<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\Contact;

class UsersController extends Controller
{
    public function create()
    {
        return view('info');
    }
 
    public function store(Request $request)
    {
        Mail::to('redatesting@gmail.Com')
            ->send(new Contact($request->except('_token')));
 
        return view('confirm');

    }

}
