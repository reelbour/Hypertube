@extends('layouts.app')

@section('content')

<form class="" action="{{url('home/search')}}" method="get" style="text-align: center;">

  <div class="field">
      <label class="label">{{ __('text.query')}}</label>
      <div class="control">
        <input type="text" name="query">
      </div>
  </div>
<!--
  <form  action="#" method="post">
    <label for="genre">Choose a gender:</label>
    <select id="genre" name="carlist" form="carform">
      <option value=""></option>
      <option value="Action">Action</option>
      <option value="Aventure">Aventure</option>
      <option value="Comedy">Comedy</option>
      <option value="Humour">Humour</option>
      <option value="Horror">Horror</option>

    </select>
    <br>
    <label for="popularity">Choose between a car:</label>
    <select id="popularity" name="carlist" form="carform">
      <option value=""></option>
      <option value="volvo">Volvo</option>
      <option value="saab">Saab</option>
      <option value="opel">Opel</option>
      <option value="audi">Audi</option>
    </select>
  </form>

  <div class="field">
      <div class="control">
        <button class="button is-link">{{ __('text.send')}}</button>
      </div>
</form>


<form>
  <label for="vol">popularity (between 0 and 10):</label>
  <input type="range" id="vol" name="vol" min="0.0" max="10">
  <label for="vol">popularity (between 0 and 10):</label>
  <input type="range" id="vol" name="vol" min="0.0" max="10">
</form>

<form>
  <label for="datemax">Enter a date before 1980-01-01:</label>
  <input type="date" id="datemax" name="datemax" max="1979"><br><br>

  <label for="datemin">Enter a date after 2000-01-01:</label>
  <input type="date" id="datemin" name="datemin" min="2000-01-02"><br><br>

  <label for="quantity">Quantity (between 1 and 5):</label>
  <input type="number" id="quantity" name="quantity" min="1" max="5">

  <label for="datemax">Enter a date before 1980-01-01:</label>
  <input type="number" step="0.1" value="0" min="0" max="10">

</form>>

<br> <br> <br> <br>

<form action="#" method="post">
  <label for="number">Enter a popularity s:</label>
  <input type="number" step="0.1" value="0" min="0" max="10">
</form> -->

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="row">
              @if (isset($movies))
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
                @else
                  <p>There is no results</p>

              @endif
            </div>
        </div>
    </div>
</div>
@endsection
