@extends('layouts.app')
@section('content')


        <div class="container">    
            <div class="row">
            
                <header class="card-header col-xs-12 col-md-12 col-sm-12 mb-3">
                    <p class="card-header-title">{{ __('text.user_profile', ['user' => $users->name])}} </p>
                </header>

                <div class="col-sm-6 col-md-4 col-xs-12">
                    <img src="{{ $users->path_picture }}" alt="Pic" class="img-rounded img-responsive img-thumbnail" />
                </div>
                <div class=" col-sm-6 col-md-8 col-xs-12">
                    <h5 class="mt-2 font-weight-bold">{{ $users->name }}</h5>
                    <table class="table table-sm table-hover table-striped">
                        <tbody>                                    
                            <tr>
                                <td>
                                    <strong>{{ __('text.last_name')}}</strong> {{ $users->last_name }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>{{ __('text.account_crea')}}</strong> {{ $users->created_at }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>{{ __('text.account_up')}}</strong> {{ $users->updated_at }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
@endsection
