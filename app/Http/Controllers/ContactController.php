<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\Contact;
 
class ContactController extends Controller
{
    public function create()
    {
        return view('contact');
    }

    public function store(ContactRequest $request)
    {
        return view('confirm');
    }

 
    public function storemail(ContactRequest $request)
    {
        Mail::to('administrateur@chezmoi.com')
            ->send(new Contact($request->except('_token')));
 
        return view('confirm');
    }
}