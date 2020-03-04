@extends('layouts.app')
@section('content')
    <div class="card">
        <header class="card-header">
            <p class="card-header-title">Modification of your profile's picture</p>
        </header>
        <div class="card-content" style="text-align: center;">
            <div class="content">
                <form action="{{ route('myprofile.store', $users->id) }}" method="POST" enctype="image/png">
                    @csrf
                    @method('post')

                    <div class="field">
                        <label class="label">Profile's Picture</label>
                        <div class="control">
                          <input id="image" type="file" class="input @error('image') is-danger @enderror" type="text" name="image" value="{{ old('image', $users->image) }}" placeholder="Your image">
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
    </div>
@endsection
