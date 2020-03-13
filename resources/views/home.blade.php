@extends('layouts.app')

@section('content')
<form class="" action="{{url('home/search')}}" method="get" style="text-align: center;">
    <div class="field">
        <div class="control">
            <input type="text" name="query" placeholder="{{ __('text.query')}}"
                value="{{ $query ?? '' }}">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
        <select name="sort">
            <option selected>Sort by</option>
            <option name="nameasc" value="nameasc">Name (asc)</option>
            <option name="namedesc" value="namedesc">Name (desc)</option>
            <option name="yearasc" value="yearasc">Year (asc)</option>
            <option name="yeardesc" value="yeardesc">Year (desc)</option>
            <option name="imdbasc" value="imdbasc">IMDb note (asc)</option>
            <option name="imdbdesc" value="imdbdesc">IMDb note (desc)</option>
        </select>

        <button type="submit" class="btn btn-secondary btn-sm">Sort</button>
    </div>
    <input type="radio" id="filtersCheck" onclick="showFilters()">
    <label for="filtersCheck">
        Filters
    </label>
    <div id="filters" style="display:none;">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="type" id="radio1" value="movies">
            <label class="form-check-label" for="radio1">Movies</label>

            <input class="form-check-input" type="radio" name="type" id="radio2" value="series">
            <label class="form-check-label" for="radio2">Series</label>
        </div>
        <div>
            <label id="imdblabel" for="imdb">IMDb note from X to 10:</label>
            <input id="imdb" type="range" min="0" max="10" onchange="imdbChange()">
        </div>
        <div>
            <label id="yearlabel" for="year">Year from X to 2020:</label>
            <input id="year" type="range" min="1940" max="2020" onchange="yearChange()">
        </div>
    </div>
</form>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="row">
                @if (isset($movies))
                    @foreach ($movies as $movie)
                        <div class="col-md-4">
                            <div class="card mb-4 shadow-sm">
                            <a href="localhost:8888/public/movie/{{$movie->title}}">
                            <img src="{{$movie->medium_cover_image}}" alt="Movie cover" width="100%">
                            </a>
                                <div class="card-body">
                                    <p class="card-text">
                                    <a href="localhost:8888/public/movie/{{$movie->title}}">{{$movie->title}}</a>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">{{$movie->year}}</small>
                                        <small class="text-muted">{{$movie->rating}}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>There is no results</p>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
    function showFilters() {
        document.getElementById("filters").style.display = "block";
    }

    function imdbChange() {
        let val = document.getElementById("imdb").value;
        document.getElementById("imdblabel").innerHTML = "IMDb note from " + val + " to 10:";
    }

    function yearChange() {
        let val = document.getElementById("year").value;
        document.getElementById("yearlabel").innerHTML = "Year from " + val + " to 2020:";
    }
</script>
@endsection
