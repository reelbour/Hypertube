@extends('layouts.app')
@section('content')
<div class="container">


    <div class="card">
        <header class="card-header">
            <p class="card-header-title">{{ __('text.user_profile', ['user' => $users->name])}} </p>
        </header>
        <div class="row">


                    <div class="container">
                    <div><img src= "{{ $users->path_picture }}" alt="Pic" style="width:200px;heigth:200px;"></div>
                    <div>
                        <ul style="list-style-type: none;">
                            <li><strong>  {{ __('text.name')}} : </strong>{{ $users->name }}</li>
                            <li><strong>  {{ __('text.last_name')}} : </strong>{{ $users->last_name }}</li>
                            <li><strong>  {{ __('text.account_crea')}} : </strong>{{ $users->created_at }}</li>
                            <li><strong>  {{ __('text.account_up')}} : </strong> {{ $users->updated_at }}</li>
                        </ul>
                    </div>
                  </div>
            </div>
        </div>
        </div>
@endsection
