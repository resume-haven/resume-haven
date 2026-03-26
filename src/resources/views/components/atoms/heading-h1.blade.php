{{-- Atom: Heading H1 (einheitliches Design für Hauptüberschriften) --}}
<h1 {{ $attributes->merge(['class' => 'text-2xl sm:text-3xl md:text-4xl lg:text-4xl font-bold text-primary dark:text-white mb-6']) }}>
    {{ $slot }}
</h1>


