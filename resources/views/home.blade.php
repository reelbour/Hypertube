@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="row">

                @foreach ($movies as $movie)

                    <div class="col-md-4">
                        <div class="card mb-4 shadow-sm">
                        <a href="localhost:8888/public/movie/{{ $movie->title}}">    <img src="{{$movie->medium_cover_image}}" alt="Movie cover">   </a>
                            <div class="card-body">
                                <p class="card-text">{{$movie->title}}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{$movie->year}}</small>
                                    <small class="text-muted">{{$movie->rating}}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
