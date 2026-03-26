{{-- Info-Card Molecule: Titel, Inhalt, optional Icon --}}
<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow flex gap-3 items-start">
    @isset($icon)
        <span class="text-3xl">{!! $icon !!}</span>
    @endisset
    <div>
        @isset($title)
            <div class="font-bold text-lg mb-1">{{ $title }}</div>
        @endisset
        <div class="text-gray-700 dark:text-gray-200">
            {{ $slot }}
        </div>
    </div>
</div>

