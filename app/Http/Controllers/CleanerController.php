<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Filmvieweds;
use App\Film;
use App\Http\Controllers\Auth;

class CleanerController extends Controller
{
  public function index()
  {
      $this->Clean_tmp();
      $date = \Carbon\Carbon::today()->subDays(30);
      $x = Film::where('updated_at', '<=', $date)->get();
      Film::where('updated_at', '<=', $date)->delete();
      foreach ($x as $key => $value)
      {
        $a = $_SERVER['DOCUMENT_ROOT'] .  "/public/film/" . $value->hash . '.mp4';
        if (file_exists($a))
          unlink($a);
      }
  }

  public  function RepEfface($dir)
  {
    $handle = opendir($dir);

    //ce while vide tous les repertoire et sous rep
    while($elem = readdir($handle))
    {
        if(is_dir($dir.'/'.$elem) && substr($elem, -2, 2) !== '..' && substr(
            $elem, -1, 1) !== '.') //si c'est un repertoire
        {
          $this->RepEfface($dir.'/'.$elem);
        }
        else
        {
          if(substr($elem, -2, 2) !== '..' && substr($elem, -1, 1) !== '.')
          {
              unlink($dir.'/'.$elem);
          }
        }

      }
      $handle = opendir($dir);
      while($elem = readdir($handle)) //ce while efface tous les dossiers
      {
          if(is_dir($dir.'/'.$elem) && substr($elem, -2, 2) !== '..' && substr(
              $elem, -1, 1) !== '.') //si c'est un repertoire
          {
              $this->RepEfface($dir.'/'.$elem);
              rmdir($dir.'/'.$elem);
          }

      }
      rmdir($dir); //ce rmdir eface le repertoire principale
    }

    public function Clean_tmp()
    {
      $i = 0;
      $array;
      $b = $_SERVER['DOCUMENT_ROOT'] .  "/public/film/";


      if ($handle = opendir($b))
      {
          while (false !== ($entry = readdir($handle)))
          {
            if ($entry != '.' && $entry != '..' && substr($entry, 40) != '_tmp')
              $array[$i++] = $entry;
          }
          closedir($handle);

          if (isset($array))
          {
          foreach ($array as $key => $value)
          {
            $filename = $_SERVER['DOCUMENT_ROOT'] .  "/public/film/" . $array[$key];
            $date = \Carbon\Carbon::today()->subDays(30);
            $date_modify = date("Y-m-d H:i:s", filemtime($filename));

            if ($date >= $date_modify)
              {

                //mtn supprimer le fichier + le directory lié
                unlink($_SERVER['DOCUMENT_ROOT'] .  "/public/film/" . $array[$key]);

                $path = substr($array[$key], 0, 40);
                $path .= '_tmp';
                $dir = $_SERVER['DOCUMENT_ROOT'] .  "/public/film/" . $path;
                if (\file_exists($dir))
                  $this->RepEfface($dir);
              }
          }
        }
        }
    }
}
