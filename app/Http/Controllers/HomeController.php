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
    // public function index()
    // {
    //   // $client = new Client([
    //   //     'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json']
    //   // ]);
    //   //
    //   // $res = $client->request('GET', 'http://tpbc.herokuapp.com/top/201');
    //   // $data = $res->getBody();
    //   //
    //   // $data = json_decode($data);
    //   // //dd($data);
    //   //
    //   // $movies = $data;
    //   //
    //   // //echo $movies->title;
    //   //  // echo $movies->years;
    //   //
    //   // return view('home', compact('movies'));
    //
    //   $client = new Client([
    //        'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json']
    //   ]);
    //
    //   $res = $client->request('GET', 'https://yts.mx/api/v2/list_movies.json?sort_by=download_count&limit=25');
    //   $data = $res->getBody();
    //   $data = json_decode($data);
    //   $movies = $data->data->movies;
    //
    //   return view('home', compact('movies'));
    // }
    public function index()
    {
      // $client = new Client(['headers' => ['content-type' => 'application/json', 'Accept' => 'application/json']]);
      // $res = $client->request('GET', 'https://eztv.io/api/get-torrents?imdb_id=1520211');
      // $data = $res->getBody();
      //
      // $data = json_decode($data);
          //dd($data);​
      //return view('home', compact('movies'));

      $client = new Client([
           'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json']
      ]);

      $res = $client->request('GET', 'https://yts.mx/api/v2/list_movies.json?sort_by=download_count&limit=15');
      $data = $res->getBody();
      $data = json_decode($data);
      $movies = $data->data->movies;

      return view('home', compact('movies'));
    }

    // public function search(Request $string)
    // {
    //   $string = $string->getRequestUri();
    //   $client = new Client([
    //       'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json']
    //   ]);
    //
    //   $string = substr($string, 26);
    //   //pri$string->query;
    //   //$string = $string->query;
    //   $x = "https://yts.mx/api/v2/list_movies.json?query_term=". "$string"  ."&limit=50&sort_by=title&order_by=asc";
    //   $res = $client->request('GET', $x);
    //   $data = $res->getBody();
    //   $data = json_decode($data);
    //
    //   if (!(isset($data->data->movies)))
    //   {
    //       return view('home')->withErrors('No results');
    //   }
    //   $movies = $data->data->movies;
    //   //sort($movies);
    //   // trier movies
    //   return view('home', compact('movies'));
    // //  https://yts.mx/api/v2/list_movies.json?query_term=tony&sort_by=download_count
    //
    // }
    public function search(Request $string)
    {
        $search = $string->getRequestUri();
        $query = substr($search, 26);
        $client = new Client([
            'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json']
        ]);

        $api = "https://yts.mx/api/v2/list_movies.json?query_term=". "$query"  ."&limit=50&sort_by=title&order_by=asc";
        $res = $client->request('GET', $api);
        $data = $res->getBody();
        $data = json_decode($data);
        $movies = [];
        if (isset($data->data->movies)) {
            foreach($data->data->movies as $res) {
                array_push($movies, (object)[
                    'title' => $res->title,
                    'year' => $res->year,
                    'rating' => $res->rating,
                    'medium_cover_image' => $res->medium_cover_image,
                    'torrents' => $res->torrents
                ]);
            }
        }

        $api = "http://www.omdbapi.com/?apikey=36cc8909&s=" . $query;
        $res = $client->request('GET', $api);
        $data = $res->getBody();
        $data = json_decode($data);
      //  dd($data);
        if (isset($data->Search)) {
            foreach ($data->Search as $res) {
                $api = "https://eztv.io/api/get-torrents?imdb_id=" . substr($res->imdbID, 2);
                $res = $client->request('GET', $api);
                $data = $res->getBody();
                $data = json_decode($data);
                //dd($data);

                if (isset($data->torrents)) {
                    foreach ($data->torrents as $res) {
                        array_push($movies, (object)[
                            'title' => $res->title,
                            'year' => date('Y', $res->date_released_unix),
                            'medium_cover_image' => $res->small_screenshot,
                            'torrents' => $res->torrent_url,
                            'rating' => ''
                        ]);
                    }
                }
            }
        }

        if (!isset($movies[0]))
            return view('home')->withErrors('No results');
        sort($movies);
        return view('home', compact('movies'));
    }
}
