<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Film extends Model
{
  protected $fillable = [
           'name', 'hash', 'time_to_live'
      ];
}
