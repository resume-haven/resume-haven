{{-- Feature-List Molecule: Liste von Features mit Icon, Titel, Beschreibung --}}
<ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    @foreach($features as $feature)
        <li class="flex items-start gap-3 p-4 bg-white dark:bg-gray-800 rounded shadow">
            @if(isset($feature['icon']))
                <span class="text-2xl">{!! $feature['icon'] !!}</span>
            @endif
            <div>
                <div class="font-semibold">{{ $feature['title'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-300">{{ $feature['description'] }}</div>
            </div>
        </li>
    @endforeach
</ul>

