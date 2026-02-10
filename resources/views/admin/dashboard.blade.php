@extends('admin.layout')

@section('content')
    <div class="top-bar">
        <div>
            <h1>Control Room</h1>
            <p class="badge">Live overview</p>
        </div>
        <div class="link-chip">Updated {{ now()->format('M j, Y') }}</div>
    </div>

    <section class="card-grid">
        <div class="card" style="animation-delay: 0.05s;">
            <h3>Total Resumes</h3>
            <div class="metric">{{ $totals['resumes'] }}</div>
        </div>
        <div class="card" style="animation-delay: 0.1s;">
            <h3>Total Users</h3>
            <div class="metric">{{ $totals['users'] }}</div>
        </div>
        <div class="card" style="animation-delay: 0.15s;">
            <h3>Status Audits</h3>
            <div class="metric">{{ $totals['status_events'] }}</div>
        </div>
    </section>

    <section class="panel">
        <div class="top-bar">
            <h1>Recent Resumes</h1>
            <a class="link-chip" href="{{ route('admin.resumes.index') }}">View all</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentResumes as $resume)
                    <tr>
                        <td>#{{ $resume->id }}</td>
                        <td>{{ $resume->name }}</td>
                        <td>{{ $resume->email }}</td>
                        <td>
                            <span class="status-pill status-{{ $resume->status }}">{{ $resume->status }}</span>
                        </td>
                        <td>
                            <a class="link-chip" href="{{ route('admin.resumes.show', $resume->id) }}">Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No resumes yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="panel">
        <div class="top-bar">
            <h1>Recent Users</h1>
            <a class="link-chip" href="{{ route('admin.users.index') }}">View all</a>
        </div>
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
                @forelse ($recentUsers as $user)
                    <tr>
                        <td>#{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No users yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
