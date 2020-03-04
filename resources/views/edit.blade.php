@extends('layouts.app')
@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Update ur account') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('myprofile.update', $users->id) }}">
                        @csrf
                        @method('put')
                        <div class="field">
                            <label class="label">Name</label>
                            <div class="control">
                              <input class="input @error('name') is-danger @enderror" type="text" name="name" value="{{ old('name', $users->name) }}" placeholder="Your name">
                            </div>
                            @error('title')
                                <p class="help is-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="field">
                            <label class="label">Last Name</label>
                            <div class="control">
                              <input class="input @error('last_name') is-danger @enderror" type="text" name="last_name" value="{{ old('last_name', $users->last_name) }}" placeholder="Your last name">
                            </div>
                            @error('title')
                                <p class="help is-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="field">
                            <label class="label">Email</label>
                            <div class="control">
                              <input class="input @error('Email') is-danger @enderror" type="text" name="Email" value="{{ old('Email', $users->email) }}" placeholder="Your Email">
                            </div>
                            @error('title')
                                <p class="help is-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="field">
                            <label class="label">Password</label>
                            <div class="control">
                              <input class="input @error('password') is-danger @enderror" type="password" name="password" value="{{ old('password', $users->password) }}" placeholder="Your password">
                            </div>
                            @error('title')
                                <p class="help is-danger">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="field">
                            <div class="control">
                              <button class="button is-link">Envoyer</button>
                            </div>
                        </div>
                        </form>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
