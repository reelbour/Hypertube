@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('text.dashboard')}}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('text.log_txt')}}

                    <ul>
                        @foreach ($movies as $movie)
                            <li>{{$movie->title}}</li>
                            <li>{{$movie->year}}</li>
                            <li>{{$movie->rating}}</li>
                            <li>{{$movie->medium_cover_image}}</li>
                            <hr>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
