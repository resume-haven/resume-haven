@extends('admin.layout')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold">Users</h1>
            <p class="mt-2 inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Managing {{ $total }} accounts</p>
        </div>
    </div>

    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="text-xs uppercase tracking-[0.2em] text-slate-400">
                    <tr>
                        <th class="px-3 py-2">ID</th>
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Email</th>
                        <th class="px-3 py-2">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-3 py-2 text-slate-500">#{{ $user->id }}</td>
                            <td class="px-3 py-2 font-medium">{{ $user->name }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $user->email }}</td>
                            <td class="px-3 py-2 text-slate-500">{{ $user->created_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-3 py-4 text-slate-500" colspan="4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 text-xs uppercase tracking-[0.2em] text-slate-400">
            <span>Page {{ $page }} of {{ $lastPage }}</span>
            <div class="flex items-center gap-2">
                @if ($page > 1)
                    <a class="rounded-full border border-slate-200 px-3 py-1 text-slate-500 transition hover:border-slate-400" href="{{ route('admin.users.index', ['page' => $page - 1]) }}">Prev</a>
                @endif
                <span class="rounded-full border border-slate-200 px-3 py-1 text-slate-500">{{ $page }}</span>
                @if ($page < $lastPage)
                    <a class="rounded-full border border-slate-200 px-3 py-1 text-slate-500 transition hover:border-slate-400" href="{{ route('admin.users.index', ['page' => $page + 1]) }}">Next</a>
                @endif
            </div>
        </div>
    </section>
@endsection
