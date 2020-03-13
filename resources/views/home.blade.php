@extends('layouts.app')

@section('content')
<form class="" action="{{url('home/search')}}" method="get" style="text-align: center;">
  <div class="field">
    <label class="label">{{ __('text.query')}}</label>
    <div class="control">
        <input type="text" name="query" value="{{ isset($query) ? $query : '' }}">

        <select class="custom-select" name="sort">
          <option selected>Sort by</option>
          <option name="nameasc" value="nameasc">Name (asc)</option>
          <option name="namedesc" value="namedesc">Name (desc)</option>
          <option name="yearasc" value="yearasc">Year (asc)</option>
          <option name="yeardesc" value="yeardesc">Year (desc)</option>
          <option name="imdbasc" value="imdbasc">IMDb note (asc)</option>
          <option name="imdbdesc" value="imdbdesc">IMDb note (desc)</option>
        </select>

        <button type="submit" class="btn btn-primary">Search</button>
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
@endsection
