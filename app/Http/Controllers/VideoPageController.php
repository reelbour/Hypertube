<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class VideoPageController extends Controller
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

        if ($_GET['type'] === 'm') {
            $api = "http://www.omdbapi.com/?apikey=36cc8909&type=movie&i=" . $_GET['imdb'];
            $res = $client->request('GET', $api);
            $data = $res->getBody();
            $omdb = json_decode($data);

            $api = 'https://yts.mx/api/v2/movie_details.json?movie_id=' . $_GET['id'];
            $res = $client->request('GET', $api);
            $data = $res->getBody();
            $data = json_decode($data);
            $movie = $data->data->movie;
            $movie = (object) [
                'title' => $movie->title,
                'id' => $movie->id,
                'year' => $movie->year,
                'rating' => $movie->rating,
                'imdb' => $movie->imdb_code,
                'torrents' => $movie->torrents,
                'plot' => $movie->description_full,
                'cover' => $movie->medium_cover_image,
                'director' => $omdb->Director,
                'country' => $omdb->Country,
                'length' => $omdb->Runtime,
                'lang' => $omdb->Language,
                'writer' => $omdb->Writer,
                'actors' => $omdb->Actors,
                'genre' => $omdb->Genre,
                'ses' => '',
                'ep' => ''
            ];
        } else {
            $api = "http://www.omdbapi.com/?apikey=36cc8909&type=series&i=" . $_GET['id'];
            $res = $client->request('GET', $api);
            $data = $res->getBody();
            $omdb = json_decode($data);

            $api = "https://eztv.io/api/get-torrents?imdb_id=" . substr($_GET['id'], 2);
            $res = $client->request('GET', $api);
            $data = $res->getBody();
            $data = json_decode($data);
            if (isset($data->torrents)) {
                foreach ($data->torrents as $res) {
                    if ($res->season === $_GET['ses'] && $res->episode === $_GET['ep']) {
                        $movie = (object) [
                            'title' => $res->title,
                            'id' => $res->id,
                            'ses' => $res->season,
                            'ep' => $res->episode,
                            'torrents' => [$res->torrent_url],
                            'year' => date('Y', $res->date_released_unix),
                            'rating' => $omdb->imdbRating,
                            'director' => $omdb->Director,
                            'country' => $omdb->Country,
                            'length' => $omdb->Runtime,
                            'lang' => $omdb->Language,
                            'writer' => $omdb->Writer,
                            'actors' => $omdb->Actors,
                            'cover' => $omdb->Poster,
                            'genre' => $omdb->Genre,
                            'imdb' => $omdb->imdbID,
                            'plot' => $omdb->Plot,
                        ];
                        break;
                    }
                }
            }
        }
        $comment = app('App\Http\Controllers\CommentController')->show($movie->id);
        return view('videoPage', compact('movie', 'comment'));
    }
}
