@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-blue-800 bg-blue-50 py-2 pe-4 ps-3 text-start text-base font-medium text-slate-900 transition duration-150 ease-in-out focus:border-blue-800 focus:bg-blue-100 focus:text-slate-900 focus:outline-none'
            : 'block w-full border-l-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-medium text-slate-700 transition duration-150 ease-in-out hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 focus:border-slate-300 focus:bg-slate-50 focus:text-slate-900 focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

