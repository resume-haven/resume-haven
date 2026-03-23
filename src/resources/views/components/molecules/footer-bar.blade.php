{{-- Footer-Bar Molecule: Copyright + Footer-Links --}}
<footer class="flex flex-col sm:flex-row justify-between bg-white dark:bg-neutral-dark border-t border-gray-200 dark:border-gray-700 text-sm dark:text-gray-200 py-6 px-4 sm:px-6 lg:px-8">
    <div class="w-full sm:w-auto text-center sm:text-left mb-2 sm:mb-0">
        <x-atoms.copyright />
    </div>
    <ul class="flex flex-col items-center gap-2 sm:flex-row sm:gap-6 sm:justify-end sm:w-auto sm:ml-auto">
        <li><x-atoms.link href="/impressum">Impressum</x-atoms.link></li>
        <li><x-atoms.link href="/datenschutz">Datenschutz</x-atoms.link></li>
        <li><x-atoms.link href="/kontakt">Kontakt</x-atoms.link></li>
        <li><x-atoms.link href="/lizenzen">Lizenzen</x-atoms.link></li>
        {{-- Weitere Footer-Links nach Bedarf --}}
    </ul>
</footer>
