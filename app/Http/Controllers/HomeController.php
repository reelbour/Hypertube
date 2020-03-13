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

        $res = $client->request('GET', 'https://yts.mx/api/v2/list_movies.json?sort_by=download_count&limit=15');
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

        if (isset($_GET['sort'])) {
            sort($movies);
            $movies = $this->dispatch_sort($_GET['sort'], $movies);
        }
        return view('home', compact('movies'));
    }

    public function search(Request $string)
    {
        $query = $_GET['query'];
        if ($query === '')
            return $this->index();
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

        $api = "http://www.omdbapi.com/?apikey=36cc8909&type=series&s=" . $query;
        $res = $client->request('GET', $api);
        $data = $res->getBody();
        $data = json_decode($data);
        if (isset($data->Search)) {
            $eps = [];
            foreach ($data->Search as $res) {
                $api = "https://eztv.io/api/get-torrents?imdb_id=" . substr($res->imdbID, 2);
                $res = $client->request('GET', $api);
                $data = $res->getBody();
                $data = json_decode($data);
                if (isset($data->torrents)) {
                    foreach ($data->torrents as $res) {
                        if (in_array(substr($res->title, 0, 5) . $res->season . $res->episode, $eps))
                            continue;
                        array_push($eps, substr($res->title, 0, 5) . $res->season . $res->episode);
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
        if (isset($_GET['sort']))
            $movies = $this->dispatch_sort($_GET['sort'], $movies);
        return view('home', compact('movies', 'query'));
    }

    private function dispatch_sort($sort, $movies) {
        switch ($sort) {
            case 'nameasc':
                return $movies;
            case 'namedesc':
                return array_reverse($movies);
            case 'yearasc':
                return $this->sort_yearasc($movies);
            case 'yeardesc':
                return $this->sort_yeardesc($movies);
            case 'imdbasc':
                return $this->sort_imdbasc($movies);
            case 'imdbdesc':
                return $this->sort_imdbdesc($movies);
            default:
                return $movies;
        }
    }

    private function sort_yearasc($movies) {
        for ($i = 0; $i < (count($movies) - 1); $i++) {
            if ($movies[$i]->year > $movies[$i + 1]->year) {
                $tmp = $movies[$i];
                $movies[$i] = $movies[$i + 1];
                $movies[$i + 1] = $tmp;
                $i = -1;
            }
        }
        return $movies;
    }

    private function sort_yeardesc($movies) {
        for ($i = 0; $i < (count($movies) - 1); $i++) {
            if ($movies[$i]->year < $movies[$i + 1]->year) {
                $tmp = $movies[$i];
                $movies[$i] = $movies[$i + 1];
                $movies[$i + 1] = $tmp;
                $i = -1;
            }
        }
        return $movies;
    }

    private function sort_imdbasc($movies) {
        for ($i = 0; $i < (count($movies) - 1); $i++) {
            if ($movies[$i]->rating > $movies[$i + 1]->rating) {
                $tmp = $movies[$i];
                $movies[$i] = $movies[$i + 1];
                $movies[$i + 1] = $tmp;
                $i = -1;
            }
        }
        return $movies;
    }

    private function sort_imdbdesc($movies) {
        for ($i = 0; $i < (count($movies) - 1); $i++) {
            if ($movies[$i]->rating < $movies[$i + 1]->rating) {
                $tmp = $movies[$i];
                $movies[$i] = $movies[$i + 1];
                $movies[$i + 1] = $tmp;
                $i = -1;
            }
        }
        return $movies;
    }
}
