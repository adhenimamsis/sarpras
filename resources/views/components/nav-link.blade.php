@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center border-b-2 border-blue-800 px-1 pt-1 text-sm font-medium leading-5 text-slate-900 transition duration-150 ease-in-out focus:border-blue-800 focus:outline-none'
            : 'inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium leading-5 text-slate-600 transition duration-150 ease-in-out hover:border-slate-300 hover:text-slate-900 focus:border-slate-300 focus:outline-none focus:text-slate-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

