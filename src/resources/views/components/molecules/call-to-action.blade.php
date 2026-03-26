{{-- Call-to-Action Molecule: Text + Button --}}
<div class="flex flex-col md:flex-row items-center gap-4 p-6 bg-blue-50 dark:bg-blue-900 rounded-lg shadow">
    <div class="flex-1">
        {{ $slot }}
    </div>
    <div>
        {{-- Button als Atom-Komponente --}}
        @isset($button)
            {{ $button }}
        @endisset
    </div>
</div>

