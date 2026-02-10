@extends('admin.layout')

@section('content')
    <div class="top-bar">
        <div>
            <h1>Resumes</h1>
            <p class="badge">Managing {{ $total }} profiles</p>
        </div>
    </div>

    <section class="panel">
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
                @forelse ($resumes as $resume)
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
                        <td colspan="5">No resumes found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            <span>Page {{ $page }} of {{ $lastPage }}</span>
            <div class="page-links">
                @if ($page > 1)
                    <a href="{{ route('admin.resumes.index', ['page' => $page - 1]) }}">Prev</a>
                @endif
                <span>{{ $page }}</span>
                @if ($page < $lastPage)
                    <a href="{{ route('admin.resumes.index', ['page' => $page + 1]) }}">Next</a>
                @endif
            </div>
        </div>
    </section>
@endsection
