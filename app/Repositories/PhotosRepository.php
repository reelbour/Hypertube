<?php
 
namespace App\Repositories;
 
use Illuminate\Http\UploadedFile;
 
class PhotosRepository
{
    public static function save(UploadedFile $image)
    {
     return   $image->store(config('images.path'), 'public');
    }
}