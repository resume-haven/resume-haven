@extends('admin.layout')

@section('content')
    <div class="top-bar">
        <div>
            <h1>Resume #{{ $resume->id }}</h1>
            <p class="badge">Profile detail</p>
        </div>
        <a class="link-chip" href="{{ route('admin.resumes.index') }}">Back to list</a>
    </div>

    <section class="card-grid">
        <div class="card">
            <h3>Name</h3>
            <div class="metric">{{ $resume->name }}</div>
        </div>
        <div class="card">
            <h3>Email</h3>
            <div class="metric">{{ $resume->email }}</div>
        </div>
        <div class="card">
            <h3>Status</h3>
            <div class="metric">
                <span class="status-pill status-{{ $resume->status }}">{{ $resume->status }}</span>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="top-bar">
            <h1>Status History</h1>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Changed At</th>
                    <th>From</th>
                    <th>To</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($history as $entry)
                    <tr>
                        <td>{{ $entry->changed_at }}</td>
                        <td><span class="status-pill status-{{ $entry->from_status }}">{{ $entry->from_status }}</span></td>
                        <td><span class="status-pill status-{{ $entry->to_status }}">{{ $entry->to_status }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No status changes recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
