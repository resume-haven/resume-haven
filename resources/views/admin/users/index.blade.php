@extends('admin.layout')

@section('content')
    <div class="top-bar">
        <div>
            <h1>Users</h1>
            <p class="badge">Managing {{ $total }} accounts</p>
        </div>
    </div>

    <section class="panel">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>#{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            <span>Page {{ $page }} of {{ $lastPage }}</span>
            <div class="page-links">
                @if ($page > 1)
                    <a href="{{ route('admin.users.index', ['page' => $page - 1]) }}">Prev</a>
                @endif
                <span>{{ $page }}</span>
                @if ($page < $lastPage)
                    <a href="{{ route('admin.users.index', ['page' => $page + 1]) }}">Next</a>
                @endif
            </div>
        </div>
    </section>
@endsection
