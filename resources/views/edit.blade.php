@extends('layouts.app')
@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('text.txt_user_up')}}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('myprofile.update', $users->id) }}">
                        @csrf
                        @method('put')
                        <div class="field">
                            <label class="label">{{ __('text.name')}}</label>
                            <div class="control">
                              <input class="input @error('name') is-danger @enderror" type="text" name="name" value="{{ old('name', $users->name) }}" placeholder="Your name">
                            </div>
                            @error('title')
                                <p class="help is-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="field">
                            <label class="label">{{ __('text.last_name')}}</label>
                            <div class="control">
                              <input class="input @error('last_name') is-danger @enderror" type="text" name="last_name" value="{{ old('last_name', $users->last_name) }}" placeholder="Your last name">
                            </div>
                            @error('title')
                                <p class="help is-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="field">
                            <label class="label">{{ __('text.email')}}</label>
                            <div class="control">
                              <input class="input @error('Email') is-danger @enderror" type="text" name="Email" value="{{ old('Email', $users->email) }}" placeholder="Your Email">
                            </div>
                            @error('title')
                                <p class="help is-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="field">
                            <label class="label">{{ __('text.password')}}</label>
                            <div class="control">
                              <input class="input @error('password') is-danger @enderror" type="current-password" name="password"  placeholder="Your password">
                            </div>
                            @error('title')
                                <p class="help is-danger">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="field">
                            <div class="control">
                              <button class="button is-link">{{ __('text.send')}}</button>
                            </div>
                        </div>
                        </form>
                            </div>
                        </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
