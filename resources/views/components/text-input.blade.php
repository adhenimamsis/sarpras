@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-slate-300 bg-white text-slate-800 placeholder:text-slate-400 shadow-sm focus:border-blue-700 focus:ring-blue-700']) }}>

