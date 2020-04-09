@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="row">
                @if (isset($movie))
                    <video id='video' width="100%" height="350px" controls controlsList="nodownload" preload="none" crossOrigin="anonymous">
                        <source src="//127.0.0.1:3000/stream/{{ $movie->hash }}" type="video/mp4">
                          @if (Auth::user()->language == 'fr')
                              <track src="http://127.0.0.1:3000/subtitles/{{ $movie->imdb }}/fr/{{ $movie->ses }}/{{ $movie->ep }}" kind="subtitles" srclang="fr" label="French" default>
                          @else
                            <track src="http://127.0.0.1:3000/subtitles/{{ $movie->imdb }}/fr/{{ $movie->ses }}/{{ $movie->ep }}" kind="subtitles" srclang="fr" label="French">
                           @endif

                          <track src="http://127.0.0.1:3000/subtitles/{{ $movie->imdb }}/en/{{ $movie->ses }}/{{ $movie->ep }}" kind="subtitles" srclang="en" label="English">


                        {{ __(('text.nosupport')) }}
                    </video>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $movie->year }}</small>
                            <small class="text-muted">{{ $movie->length }}</small>
                            <small class="text-muted">{{ $movie->rating }}/10 IMDb</small>
                        </div>
                        <br />
                        <h4 class="card-text" style="text-align: center">
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

                <ul class="list-unstyled">


                  @foreach ($comment as $comments)

                  <li class="media my-4">
                    <div class="media-body">
                      <h5 class="mt-0 mb-1"><a href="{{url('UserProfile/' . $comments->user_id)}}">{{$comments->name}}</a></h5>
                      {{$comments->content}}
                      <div class="d-flex justify-content-between align-items-center">
                          <small class="text-muted">{{ $comments->created_at }}</small>
                        </div>
                      </div>
                  </li>

                  @endforeach
                  <li class="media">

                    <div class="media-body">
                      <form action="{{ route('comment.store')}}" method="post">
                        @csrf
                        <!-- @method('PUT') -->
                        <input type="hidden" name="id" value="{{ $movie->id}}">
                        <textarea name="content" cols="40" rows="5" placeholder="{{ __(('text.comment')) }}"></textarea>
                        <!-- <input type="text" name="content" value="" col=5 row=15 placeholder="Your comment"> -->
                        <button class="button is-danger" type="submit">{{ __(('text.send')) }}</button>
                      </form>
                    </div>
                  </li>
                </ul>

            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>

<script>
        $(document).ready(function() {
          $('#video').on('play', function(e) {
            $.ajaxSetup({
           headers: {
           'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>'
              }
            });

            $.ajax({
           url: '/public/viewed',
                type: 'POST',
                data: {
                  id_movie: {{$movie->id}},
                  hash: '{{$movie->hash}}',
                  title: '{{$movie->title}}',
                  id_user: {{auth()->id()}}
                },

           dataType: 'JSON'
         });
          })
      })
</script>
@endsection
