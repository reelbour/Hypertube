<?php

namespace App\Http\Controllers;

use App\Htpp\Requests\ImagesRequest;

class PicturesController extends Controller
{
    //
    public static function save(UploadedFile $image)
    {
      return $image->store(config('images.path'), 'public');
    }
}
