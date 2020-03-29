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

                        <h4 class="card-text" style="text-align: center">
                            {{ $movie->title }}
                        </h4>


                          <p class="card-text" style="text-align: right">
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



            <form action="{{ route('comment.store')}}" method="post">
              @csrf
              <!-- @method('PUT') -->
              <input type="hidden" name="imdb" value="{{ $movie->imdb}}">
              <input type="text" name="content" value="" placeholder="Your comment">
              <button class="button is-danger" type="submit">Ajouter</button>
            </form>


            <!-- ici mettre les commentaires -->


                <div class="card-body">

                        @foreach ($comment as $comments)
                            <div class="card-text">

                              <p>added by <a href="{{url('UserProfile/' . $comments->user_id)}}">{{$comments->user_id}}</a> </p>
                          <p>Created at :{{$comments->created_at}}</p>
                          <p>Content:   {{$comments->content}}</p>

                            </div>
              </div>
                        @endforeach

        </div>
    </div>
</div>
@endsection
