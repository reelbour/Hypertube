<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Filmvieweds;
use App\Film;
use Illuminate\Database\Eloquent\Model;


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
        $movies = [];
        if (isset($data->data->movies)) {
            foreach($data->data->movies as $res) {
                array_push($movies, (object)[
                    'title' => $res->title,
                    'id' => $res->id,
                    'year' => $res->year,
                    'genres' => $res->genres,
                    'rating' => $res->rating,
                    'imdb' => $res->imdb_code,
                    'torrents' => $res->torrents,
                    'cover' => $res->medium_cover_image,
                    'ses' => '',
                    'ep' => '',
                    'movie_viewed' => Filmvieweds::where('user_id', auth()->id())->where('name', $res->title)->get()
                ]);
            }
        }

        if (isset($_GET['sort'])) {
            $default = $movies;
            sort($movies);
            $movies = $this->dispatch_sort($_GET['sort'], $movies, $default);

            if (isset($_GET['filters']))
                $movies = $this->filter($movies);
            if (isset($_GET['type']) && $_GET['type'] === 'series')
                $movies = [];
        }

        $x = compact('movies');
        return view('home', $x);
    }

    public function search(Request $string)
    {
        $movies = [];
        $query = $_GET['query'];
        if ($query === '')
            return $this->index();
        if($query != strip_tags($query))
          return $this->index();

        $client = new Client([
            'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json']
        ]);

        if (!isset($_GET['type']) || (isset($_GET['type']) && $_GET['type'] === 'movies')) {
            $api = "https://yts.mx/api/v2/list_movies.json?query_term=". "$query"  ."&limit=50&sort_by=title&order_by=asc";
            $res = $client->request('GET', $api);
            $data = $res->getBody();
            $data = json_decode($data);
            if (isset($data->data->movies)) {
                foreach($data->data->movies as $res) {
                    array_push($movies, (object)[
                        'title' => $res->title,
                        'id' => $res->id,
                        'year' => $res->year,
                        'genres' => $res->genres,
                        'rating' => $res->rating,
                        'imdb' => $res->imdb_code,
                        'torrents' => $res->torrents,
                        'cover' => $res->medium_cover_image,
                        'ses' => '',
                        'ep' => '',
                        'movie_viewed' => Filmvieweds::where('user_id', auth()->id())->where('name', $res->title)->get()
                    ]);
                }
            }
        }

        if (!isset($_GET['type']) || (isset($_GET['type']) && $_GET['type'] === 'series')) {
            $api = "http://www.omdbapi.com/?apikey=36cc8909&type=series&s=" . $query;
            $res = $client->request('GET', $api);
            $data = $res->getBody();
            $data = json_decode($data);
            if (isset($data->Search)) {
                $eps = [];
                foreach ($data->Search as $result) {
                    $api = "https://eztv.io/api/get-torrents?imdb_id=" . substr($result->imdbID, 2);
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
                                'id' => $res->id,
                                'ses' => $res->season,
                                'ep' => $res->episode,
                                'torrents' => [$res->torrent_url],
                                'year' => date('Y', $res->date_released_unix),
                                'cover' => $result->Poster,
                                'imdb' => $result->imdbID,
                                'rating' => '',
                                'genres' => [],
                                'movie_viewed' => Filmvieweds::where('user_id', auth()->id())->where('name', $res->title)->get()
                            ]);
                        }
                    }
                }
            }
        }

        if (!isset($movies[0]))
            return view('home')->withErrors('No results');

        sort($movies);
        if (isset($_GET['filters']))
            $movies = $this->filter($movies);
        if (isset($_GET['sort']))
            $movies = $this->dispatch_sort($_GET['sort'], $movies, $movies);
        return view('home', compact('movies', 'query'));
    }

    private function filter($movies) {
        $new = [];
        $year = (int) $_GET['year'];

        if (isset($_GET['imdb']) && !isset($_GET['genre'])) {
            $imdb = (int) $_GET['imdb'];
            foreach ($movies as $mov)
                if ($mov->rating >= $imdb && $mov->year >= $year)
                    array_push($new, $mov);
        } else if (isset($_GET['genre']) && !isset($_GET['imdb'])) {
            foreach ($movies as $mov)
                foreach ($_GET['genre'] as $genre)
                    if ($mov->year >= $year && in_array($genre, $mov->genres))
                        array_push($new, $mov);
        } else if (isset($_GET['imdb']) && isset($_GET['genre'])) {
            $imdb = (int) $_GET['imdb'];
            foreach ($movies as $mov)
                foreach ($_GET['genre'] as $genre)
                    if ($mov->rating >= $imdb && $mov->year >= $year
                    && in_array($genre, $mov->genres))
                        array_push($new, $mov);
        } else {
            foreach ($movies as $mov)
                if ($mov->year >= $year)
                    array_push($new, $mov);
        }

        return $new;
    }

    private function dispatch_sort($sort, $movies, $default) {
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
                return $default;
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
