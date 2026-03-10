@extends('layouts.app')

@section('title', 'Kontakt')

@section('content')
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-6">Kontakt</h1>

        @if (session('success'))
            <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('contact.submit') }}" class="space-y-4 sm:space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium mb-2">Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-base min-h-[48px] dark:bg-neutral-dark dark:border-gray-600 dark:text-text-dark"
                    required
                >
            </div>

            <div>
                <label for="email" class="block text-sm font-medium mb-2">E-Mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-base min-h-[48px] dark:bg-neutral-dark dark:border-gray-600 dark:text-text-dark"
                    required
                >
            </div>

            <div>
                <label for="message" class="block text-sm font-medium mb-2">Nachricht</label>
                <textarea
                    id="message"
                    name="message"
                    rows="6"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary resize-none text-base min-h-[150px] dark:bg-neutral-dark dark:border-gray-600 dark:text-text-dark"
                    required
                >{{ old('message') }}</textarea>
            </div>

            <button
                type="submit"
                class="w-full sm:w-auto bg-primary hover:bg-primary-dark text-white font-semibold px-8 py-3 rounded-lg transition-colors min-h-[48px] text-base"
            >
                Absenden
            </button>
        </form>
    </div>
@endsection
