@extends('layouts.app')
@section('content')
    <div class="card">
        <header class="card-header">
            <p class="card-header-title">User Profile</p>
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
                                <td><strong>{{ $users->created_at }}</strong></td>
                                <td><strong>{{ $users->updated_at }}</strong></td>
                            </tr>


                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
