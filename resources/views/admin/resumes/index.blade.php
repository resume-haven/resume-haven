@extends('admin.layout')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold">Resumes</h1>
            <p class="mt-2 inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Managing {{ $total }} profiles</p>
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
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($resumes as $resume)
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
                            <td class="px-3 py-4 text-slate-500" colspan="5">No resumes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 text-xs uppercase tracking-[0.2em] text-slate-400">
            <span>Page {{ $page }} of {{ $lastPage }}</span>
            <div class="flex items-center gap-2">
                @if ($page > 1)
                    <a class="rounded-full border border-slate-200 px-3 py-1 text-slate-500 transition hover:border-slate-400" href="{{ route('admin.resumes.index', ['page' => $page - 1]) }}">Prev</a>
                @endif
                <span class="rounded-full border border-slate-200 px-3 py-1 text-slate-500">{{ $page }}</span>
                @if ($page < $lastPage)
                    <a class="rounded-full border border-slate-200 px-3 py-1 text-slate-500 transition hover:border-slate-400" href="{{ route('admin.resumes.index', ['page' => $page + 1]) }}">Next</a>
                @endif
            </div>
        </div>
    </section>
@endsection
