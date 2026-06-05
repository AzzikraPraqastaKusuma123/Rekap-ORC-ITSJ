<label {{ $attributes->merge(['class' => 'block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
