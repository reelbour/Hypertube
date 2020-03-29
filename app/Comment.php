<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
  protected $fillable = [
      'id', 'user_id', 'imdb_id', 'content'
  ];
}
