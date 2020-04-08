<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Filmvieweds;
use App\Film;
use App\Http\Controllers\Auth;

class CleanerController extends Controller
{
    // ce controlleur s occupe de recuperer in db les films telecharger   OK
    // check si la date du dernier update depasse 30 jours                OK


    // si oui suppression
    // si des fichiers/dossiers tmp existe depuis plus que 3 jours suppression.

    // ce controlleur dois etre appeler au login ou logout

    //1 . on recuperer les films telecharger

  public function index()
{

    ///** ici on gere la suopression dans la db + avant on recupere dans x la liste des films a supprimer
    //***
    $date = \Carbon\Carbon::today()->subDays(30);
    $x = Film::where('updated_at', '<=', $date)->get();
    //Film::where('updated_at', '<=', $date)->delete();
    //**
    ///**** mtn check le x pour supprimer le fichier dont le hash correspond a chaque case du tableau
    foreach ($x as $key => $value) {
      //echo $value->hash . '<br>';
      $a = url("/film/" . $value->hash . '.mp4');
       echo $a;
      if (file_exists(url("/film/" . $value->hash . '.mp4')) == False)
        {
          //Storage::disk('public');
          echo 'sosaaaaaaaaaaa <br><br>';
          unlink(url('/film/' . $value->hash . '.mp4'));
        }
      else {
        echo 'n existe pâs<br>';
      }
    }

    //$x =  Film::all();
    // foreach ($x as $key => $value) {
    //
    //   if ($x->updated_at)
    //     echo 1;
    //
    //   echo '<br>sosa<br><br>';
    // }
     dd($x);
}


}
