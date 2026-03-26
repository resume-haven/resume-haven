<div class="flex items-center gap-3 mb-6">
    @if (!empty($logo))
        <x-atoms.logo />
    @endif
    @if (!empty($brandname))
        <x-atoms.brandname />
    @endif
    {{--
        Branding/Markenname: KEIN <h1>!
        Das Branding ist kein semantischer Seitenhaupttitel, sondern reines Markenelement.
        Für SEO und Barrierefreiheit darf pro Seite nur EIN <h1> vorkommen (siehe Atoms/heading-h1).
        Das Layout bleibt durch die Tailwind-Klassen identisch.
    --}}
    <div class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary dark:text-white">
        {{ $slot }}
    </div>
</div>

