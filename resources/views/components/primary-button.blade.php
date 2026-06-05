<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-tr from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-wider shadow-lg shadow-blue-500/10 hover:shadow-blue-500/20 active:scale-[0.98] transition-all duration-200 focus:outline-none focus:ring-1 focus:ring-blue-500']) }}>
    {{ $slot }}
</button>
