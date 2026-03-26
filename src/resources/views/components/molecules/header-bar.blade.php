{{-- Kombiniert Logo, Brandname, Navigation und optional Darkmode-Toggle --}}
<header class="flex items-center justify-between py-4 px-6 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
    <div class="flex items-center gap-2">
        <x-atoms.logo />
        <x-atoms.brandname />
    </div>
    <nav>
        <x-molecules.nav-menu />
    </nav>
    <div>
        <x-atoms.darkmode-button />
    </div>
</header>

