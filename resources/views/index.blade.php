@extends('myprofile')
@section('content')
    <div class="card">
        <header class="card-header">
            <p class="card-header-title">My profile</p>
        </header>
        <div class="card-content">
            <div class="content">
                <table class="table is-hoverable">
                    <thead>
                        <tr>
                        <th>Profile's Picture</th>
                            <th>#</th>
                            <th>Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Account created at</th>
                            <th>Account updated at</th>

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

                                <td><a href="#">   Updater your profile's picture ?</a></td>
                                <td></td>
                                <td></td>
                                <td><td><a href="{{ route('myprofile.edit', $users->id) }}">Updater your user's information ?</a></td></td>

                            </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
