<button {{ $attributes->merge(['type' => 'submit', 'class' => 'px-4 py-2 rounded-lg font-semibold transition']) }}>
    {{ $slot }}
</button>

