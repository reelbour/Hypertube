@extends('layouts.app')

@section('content')
<form class="" action="{{url('home/search')}}" method="get" style="text-align: center;">
    <div class="field">
        <div class="control">
            <input type="text" name="query" placeholder="{{ __('text.query') }}"
                value="{{ $query ?? '' }}">
            <button type="submit" class="btn btn-primary">{{ __('text.searchbtn') }}</button>
        </div>
        <select name="sort">
            <option selected>{{ __('text.sortby') }}</option>
            <option name="nameasc" value="nameasc">{{ __('text.name') }} (asc)</option>
            <option name="namedesc" value="namedesc">{{ __('text.name') }} (desc)</option>
            <option name="yearasc" value="yearasc">{{ __('text.year') }} (asc)</option>
            <option name="yeardesc" value="yeardesc">{{ __('text.year') }} (desc)</option>
            <option name="imdbasc" value="imdbasc">{{ __('text.imdb') }} (asc)</option>
            <option name="imdbdesc" value="imdbdesc">{{ __('text.imdb') }} (desc)</option>
        </select>

        <button type="submit" class="btn btn-secondary btn-sm">{{ __('text.sortbtn') }}</button>
    </div>
    <input type="radio" id="filtersCheck" onclick="showFilters()">
    <label for="filtersCheck">
        {{ __('text.filters') }}
    </label>
    <div id="filters" style="display:none;">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="type" id="radio1" value="movies"
                onclick="showSpecificFilters()">
            <label class="form-check-label" for="radio1">{{ __('text.movies') }}</label>

            <input class="form-check-input" type="radio" name="type" id="radio2" value="series"
                onclick="hideSpecificFilters()">
            <label class="form-check-label" for="radio2">{{ __('text.series') }}</label>
        </div>
        <div id="imdbrange" style="display:none">
            <p id="imdblabeltxt" style="display:none">{{ __('text.imdbrange') }}</p>
            <label id="imdblabel" for="imdb"></label>
            <input id="imdb" type="range" min="0" max="10" name="imdb" onchange="imdbChange()" disabled>
        </div>
        <div id="yearrange">
            <p id="yearlabeltxt" style="display:none">{{ __('text.yearrange') }}</p>
            <label id="yearlabel" for="year"></label>
            <input id="year" type="range" min="1940" max="2020" name="year" onchange="yearChange()" disabled>
        </div>
        <select id="genre" name="genre[]" style="display:none" multiple>
            <option name="action" value="action">{{ __('text.action') }}</option>
            <option name="adventure" value="adventure">{{ __('text.action') }}</option>
            <option name="animation" value="animation">{{ __('text.animation') }}</option>
            <option name="biography" value="biography">{{ __('text.biography') }}</option>
            <option name="comedy" value="comedy">{{ __('text.comedy') }}</option>
            <option name="crime" value="crime">{{ __('text.crime') }}</option>
            <option name="documentary" value="documentary">{{ __('text.documentary') }}</option>
            <option name="drama" value="drama">{{ __('text.drama') }}</option>
            <option name="family" value="family">{{ __('text.family') }}</option>
            <option name="fantasy" value="fantasy">{{ __('text.fantasy') }}</option>
            <option name="gameshow" value="gameshow">{{ __('text.gameshow') }}</option>
            <option name="history" value="history">{{ __('text.history') }}</option>
            <option name="horror" value="horror">{{ __('text.horror') }}</option>
            <option name="music" value="music">{{ __('text.music') }}</option>
            <option name="musical" value="musical">{{ __('text.musical') }}</option>
            <option name="mistery" value="mistery">{{ __('text.mistery') }}</option>
            <option name="news" value="news">{{ __('text.news') }}</option>
            <option name="reality-tv" value="reality-tv">{{ __('text.reality-tv') }}</option>
            <option name="romance" value="romance">{{ __('text.romance') }}</option>
            <option name="sci-fi" value="sci-fi">{{ __('text.sci-fi') }}</option>
            <option name="sport" value="sport">{{ __('text.sport') }}</option>
            <option name="superhero" value="superhero">{{ __('text.superhero') }}</option>
            <option name="talkshow" value="talkshow">{{ __('text.talkshow') }}</option>
            <option name="thriller" value="thriller">{{ __('text.thriller') }}</option>
            <option name="war" value="war">{{ __('text.war') }}</option>
            <option name="western" value="western">{{ __('text.western') }}</option>
        </select>

        <button type="submit" class="btn btn-secondary btn-sm">{{ __('text.filterbtn') }}</button>
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
        document.getElementById("year").disabled = false;

        val = document.getElementById("year").value;
        txt = document.getElementById("yearlabeltxt").textContent;
        document.getElementById("yearlabel").innerHTML = txt + val + "->2020";
    }

    function imdbChange() {
        let val = document.getElementById("imdb").value;
        let txt = document.getElementById("imdblabeltxt").textContent;
        document.getElementById("imdblabel").innerHTML = txt + val + "->10";
    }

    function yearChange() {
        let val = document.getElementById("year").value;
        let txt = document.getElementById("yearlabeltxt").textContent;
        document.getElementById("yearlabel").innerHTML = txt + val + "->2020";
    }

    function showSpecificFilters() {
        document.getElementById("imdb").disabled = false;
        document.getElementById("genre").disabled = false;
        document.getElementById("imdbrange").style.display = "block";
        document.getElementById("genre").style.display = "inline-block";

        let val = document.getElementById("imdb").value;
        let txt = document.getElementById("imdblabeltxt").textContent;
        document.getElementById("imdblabel").innerHTML = txt + val + "->10";
    }

    function hideSpecificFilters() {
        document.getElementById("genre").style.display = "none";
        document.getElementById("imdbrange").style.display = "none";

        document.getElementById("genre").disabled = true;
        document.getElementById("imdbrange").disabled = true;
    }
</script>
@endsection
