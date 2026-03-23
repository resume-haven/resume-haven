<div class="flex items-center gap-3 mb-6">
    @if (!empty($logo))
        <x-atoms.logo />
    @endif
    @if (!empty($brandname))
        <x-atoms.brandname />
    @endif
    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary dark:text-white">
        {{ $slot }}
    </h1>
</div>

