<?php

namespace App\Http\Middleware;

use Closure;

class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
     public function handle($request, Closure $next)
     {
          //$users = auth()->user();


          if (isset(auth()->user()->language))
          {
            $l  = auth()->user()->language;

            \App::setLocale($l);
          }
         //dd($request);

         return $next($request);
     }
}
