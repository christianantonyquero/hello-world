@extends('layouts.admin')

@section('title', 'Users')

@section('header-buttons')
    <button type="button" class="btn btn-new btn-sm mr-2" data-toggle="modal" data-target="#addUserModal">New User</button>
    <a href="{{ route('admin.users.export') }}" class="btn btn-sm btn-dark export-users">Export Users</a>
@endsection

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Total Users: {{ number_format($users->total(), 0)}}</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">User</th>
                        <th scope="col">Admin?</th>
                        <th scope="col">Tester?</th>
                        <th>Login Count</th>
                        <th>Last Login At</th>
                        <th>Created At</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                {{ $user->name }} <br>
                                <small>{{ $user->email }}</small>
                            </td>
                            <td>{{ $user->is_admin ? 'Yes' : 'No' }}</td>
                            <td>{{ $user->is_tester ? 'Yes' : 'No' }}</td>
                            <td>
                                {{ $user->login_count }}
                            </td>
                            <td>
                                @if( $user->last_login_at)
                                    {{ $user->last_login_at->diffForHumans()}} <br/>
                                    <small>{{ $user->last_login_at->format('m-d-Y H:i')}}</small>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('m-d-Y H:i') }}</td>

                            <td class="text-right button-cell">
                                <button type="button" class="btn btn-primary btn-sm" href="#" dusk="user-{{ $user->id }}-edit" data-toggle="modal" data-target="#editUserModal" onclick="app.$refs.editUser.loadUser({{ $user->id }})">
                                    Edit
                                </button>

                                <form class="inline-form" action="{{ route('admin.users.delete', $user) }}" method="POST">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"
>
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $users->links() }} 
        </div>
    </div>
</div>

@endsection