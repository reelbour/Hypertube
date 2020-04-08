<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Filmvieweds extends Model
{
  protected $fillable = [
           'user_id', 'id_movie', 'name', 'hash'
      ];
}
