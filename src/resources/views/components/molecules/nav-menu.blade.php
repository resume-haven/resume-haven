{{-- Navigation-Menü als Molecule --}}
<ul class="flex gap-4">
    <li><x-atoms.link href="/">Home</x-atoms.link></li>
    <li><x-atoms.link href="/analyse">Analyse</x-atoms.link></li>
    {{-- Weitere Links nach Bedarf --}}
    @auth
        <li><x-atoms.link href="/logout">Logout</x-atoms.link></li>
    @else
        <li><x-atoms.link href="/login">Login</x-atoms.link></li>
    @endauth
</ul>

