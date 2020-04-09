@extends('myprofile')
@section('content')


<div class="card">
    <header class="card-header">
        <p class="card-header-title">{{ __('text.user_profile', ['user' => $users->name])}} </p>
    </header>
    <div class="row">


                <div class="container">
                <div><img src= "{{ $users->path_picture }}" alt="Pic" style="width:300px;heigth:300px;"></div>
                <div>
                    <ul style="list-style-type: none;">
                        <li><strong>  {{ __('text.name')}} : </strong>{{ $users->name }}</li>
                        <li><strong>  {{ __('text.last_name')}} : </strong>{{ $users->last_name }}</li>
                        <li><strong>  {{ __('text.email')}} : </strong>{{ $users->email }}</li>
                        <li><strong>  {{ __('text.account_crea')}} : </strong>{{ $users->created_at }}</li>
                        <li><strong>  {{ __('text.account_up')}} : </strong> {{ $users->updated_at }}</li>
                    </ul>

                      <a href="{{ route('myprofile.show', $users->id) }}">{{ __('text.pic_up_btn') }}</a><br>
                      <a href="{{ route('myprofile.edit', $users->id) }}">{{ __('text.profile_up_btn') }}</a>
                </div>
              </div>
        </div>
    </div>
    </div>

@endsection
