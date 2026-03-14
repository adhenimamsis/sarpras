<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-xl border border-blue-800 bg-blue-800 px-4 py-2 text-sm font-medium text-white shadow-sm transition ease-in-out duration-150 hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 focus:ring-offset-white active:bg-blue-900']) }}>
    {{ $slot }}
</button>


