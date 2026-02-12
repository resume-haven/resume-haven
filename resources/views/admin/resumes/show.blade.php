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
            <h1>Admin Actions</h1>
        </div>
        <div class="card-grid">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-semibold">Resume #{{ $resume->id }}</h1>
                        <p class="mt-2 inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Profile detail</p>
                    </div>
                    <a class="rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 transition hover:border-slate-400" href="{{ route('admin.resumes.index') }}">Back to list</a>
                </div>

                <section class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-500">Name</h3>
                        <div class="mt-2 text-xl font-semibold">{{ $resume->name }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-500">Email</h3>
                        <div class="mt-2 text-xl font-semibold">{{ $resume->email }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-500">Status</h3>
                        <div class="mt-2">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $resume->status }}</span>
                        </div>
                    </div>
                </section>

                <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-xl font-semibold">Status History</h2>
                    </div>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="text-xs uppercase tracking-[0.2em] text-slate-400">
                                <tr>
                                    <th class="px-3 py-2">Changed At</th>
                                    <th class="px-3 py-2">From</th>
                                    <th class="px-3 py-2">To</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($history as $entry)
                                    <tr>
                                        <td class="px-3 py-2 text-slate-500">{{ $entry->changed_at }}</td>
                                        <td class="px-3 py-2">
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $entry->from_status }}</span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $entry->to_status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-3 py-4 text-slate-500" colspan="3">No status changes recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-xl font-semibold">Admin Actions</h2>
                    </div>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <form method="POST" action="{{ route('admin.resumes.status', $resume->id) }}" class="rounded-2xl border border-slate-200 p-5">
                            @csrf
                            @method('PATCH')
                            <h3 class="text-sm font-semibold text-slate-500">Update Status</h3>
                            <div class="mt-3">
                                <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-200">
                                    @foreach (['draft', 'published', 'archived'] as $status)
                                        <option value="{{ $status }}" @selected($resume->status === $status)>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="mt-4 rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white transition hover:bg-slate-800" type="submit">Save</button>
                        </form>

                        <form method="POST" action="{{ route('admin.resumes.destroy', $resume->id) }}" class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
                            @csrf
                            @method('DELETE')
                            <h3 class="text-sm font-semibold text-rose-700">Delete Resume</h3>
                            <p class="mt-2 text-xs uppercase tracking-[0.2em] text-rose-500">This cannot be undone.</p>
                            <button class="mt-4 rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white transition hover:bg-rose-500" type="submit">Delete</button>
                        </form>
                    </div>
                </section>
