@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'text-sm font-medium text-blue-800']) }}>
        {{ $status }}
    </div>
@endif

