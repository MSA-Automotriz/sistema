@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-slate-300 bg-white text-slate-900 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition duration-150 ease-in-out disabled:bg-slate-50 disabled:text-slate-500']) !!}>
