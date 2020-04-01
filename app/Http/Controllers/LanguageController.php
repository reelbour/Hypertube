<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;


class LanguageController extends Controller
{
    public function getLang() {
        return \App::getLocale();
    }

    public function setLang($lang){
        \App::setLocale($lang);

        // mettre la valeur dans bdd
          $users = auth()->user();

        $id  = auth()->id();

        $users->where('id', $id)->update(["language" => $lang]);

        return redirect()->back();
    }
}
