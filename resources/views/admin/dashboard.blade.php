@extends('admin.layout')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold">Control Room</h1>
            <p class="mt-2 inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Live overview</p>
        </div>
        <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs uppercase tracking-[0.2em] text-slate-500">
            Updated {{ now()->format('M j, Y') }}
        </div>
    </div>

    <section class="mt-8 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-500">Total Resumes</h3>
            <div class="mt-2 text-3xl font-semibold">{{ $totals['resumes'] }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-500">Total Users</h3>
            <div class="mt-2 text-3xl font-semibold">{{ $totals['users'] }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-500">Status Audits</h3>
            <div class="mt-2 text-3xl font-semibold">{{ $totals['status_events'] }}</div>
        </div>
    </section>

    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold">Recent Resumes</h2>
            <a class="rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 transition hover:border-slate-400" href="{{ route('admin.resumes.index') }}">View all</a>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="text-xs uppercase tracking-[0.2em] text-slate-400">
                    <tr>
                        <th class="px-3 py-2">ID</th>
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Email</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentResumes as $resume)
                        <tr>
                            <td class="px-3 py-2 text-slate-500">#{{ $resume->id }}</td>
                            <td class="px-3 py-2 font-medium">{{ $resume->name }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $resume->email }}</td>
                            <td class="px-3 py-2">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $resume->status }}</span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <a class="rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 transition hover:border-slate-400" href="{{ route('admin.resumes.show', $resume->id) }}">Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-3 py-4 text-slate-500" colspan="5">No resumes yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold">Recent Users</h2>
            <a class="rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 transition hover:border-slate-400" href="{{ route('admin.users.index') }}">View all</a>
        </div>
        <div class="mt-4 overflow-x-auto">
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
                    @forelse ($recentUsers as $user)
                        <tr>
                            <td class="px-3 py-2 text-slate-500">#{{ $user->id }}</td>
                            <td class="px-3 py-2 font-medium">{{ $user->name }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $user->email }}</td>
                            <td class="px-3 py-2 text-slate-500">{{ $user->created_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-3 py-4 text-slate-500" colspan="4">No users yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
