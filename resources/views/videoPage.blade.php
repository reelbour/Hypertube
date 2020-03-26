@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="row">
                @if (isset($movie))
                    <video width="100%" height="350px" controls>
                        <source src="" type="video/mp4">
                        {{ __(('text.nosupport')) }}
                    </video>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $movie->year }}</small>
                            <small class="text-muted">{{ $movie->length }}</small>
                            <small class="text-muted">{{ $movie->rating }}/10 IMDb</small>
                        </div>
                        <br />

                        <h4 class="card-text">
                            {{ $movie->title }}
                        </h4>
                        <p class="card-text">
                            {{ $movie->plot }}
                        </p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><span class="font-weight-bold">Main actor(s): </span >
                                {{ $movie->actors }}
                            </li>
                            <li class="list-group-item"><span class="font-weight-bold">Director(s): </span >
                                {{ $movie->director }}
                            </li>
                            <li class="list-group-item"><span class="font-weight-bold">Writer(s): </span >
                                {{ $movie->writer }}
                            </li>
                        </ul>
                    </div>
                @endif
            </div>

            <!-- {{ $x = $_GET['imdb']}} -->
            <form action="{{ url("/comment") }}" method="post">
              @csrf
              <div class="row justify-content-center">
                <textarea name="comment" placeholder="{{ __('text.comment')}}" rows="10" cols="80"></textarea>
              </div>

              <button class="btn btn-dark" type="button" name="button">{{ __('text.send')}}</button>
            </form>
        </div>
    </div>
</div>
@endsection
