@extends('myprofile')
@section('content')
    <div class="card">
        <header class="card-header">
            <p class="card-header-title">{{ __('text.my_profile') }}</p>
        </header>
        <div class="card-content">
            <div class="content">
                <table class="table is-hoverable">
                    <thead>
                        <tr>
                        <th>{{ __('text.profilepic') }}</th>
                            <th>#</th>
                            <th>{{ __('text.name') }}</th>
                            <th>{{ __('text.last_name') }}</th>
                            <th>{{ __('text.email') }}</th>
                            <th>{{ __('text.account_crea') }}</th>
                            <th>{{ __('text.account_up') }}</th>

                        </tr>
                    </thead>
                    <tbody>

                            <tr>
                            <td><img src= "{{ $users->path_picture }}" alt="" width=500px; heigth=500px;></td>
                                <td>{{ $users->id }}</td>
                                <td><strong>{{ $users->name }}</strong></td>
                                <td><strong>{{ $users->last_name }}</strong></td>
                                <td><strong>{{ $users->email }}</strong></td>
                                <td><strong>{{ $users->created_at }}</strong></td>
                                <td><strong>{{ $users->updated_at }}</strong></td>
                            </tr>
                            <tr>

                                <td><a href="{{ route('myprofile.show', $users->id) }}">{{ __('text.pic_up_btn') }}</a></td>
                                <td></td>
                                <td></td>
                                <td><td><a href="{{ route('myprofile.edit', $users->id) }}">{{ __('text.profile_up_btn') }}</a></td></td>

                            </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection