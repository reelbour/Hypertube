<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $client = new Client([
            'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json']
        ]);

        $res = $client->request('GET', 'https://yts.mx/api/v2/list_movies.json?sort_by=download_count&limit=50');
        $data = $res->getBody();
        $data = json_decode($data);
        $movies = $data->data->movies;

        return view('home', compact('movies'));
    }

    public function search(Request $string)
    {
      $string = $string->getRequestUri();
      $client = new Client([
          'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json']
      ]);

      $string = substr($string, 26);
      //pri$string->query;
      //$string = $string->query;
      $x = "https://yts.mx/api/v2/list_movies.json?query_term=". "$string"  ."&limit=50&sort_by=title&order_by=asc";
      $res = $client->request('GET', $x);
      $data = $res->getBody();
      $data = json_decode($data);

      if (!(isset($data->data->movies)))
      {
          return view('home')->withErrors('No results');
      }
      $movies = $data->data->movies;
      //sort($movies);
      // trier movies
      return view('home', compact('movies'));
    //  https://yts.mx/api/v2/list_movies.json?query_term=tony&sort_by=download_count

    }
}
