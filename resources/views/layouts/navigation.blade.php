{{-- Navigation partial for mobile sidebar drawer --}}
<a href="{{ route('dashboard') }}"
   class="nav-link flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all relative group
          {{ request()->routeIs('dashboard') ? 'nav-link-active text-blue-400' : 'text-slate-400 hover:text-white' }}">
    <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-all overflow-hidden"
          style="min-width:32px;min-height:32px;max-width:32px;max-height:32px;
                 {{ request()->routeIs('dashboard') ? 'background:rgba(59,130,246,0.15);color:#60a5fa;' : 'background:rgba(255,255,255,0.05);' }}">
        <svg style="width:16px;height:16px;min-width:16px;min-height:16px;max-width:16px;max-height:16px;flex-shrink:0;display:block;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
        </svg>
    </span>
    <span class="font-display">Dashboard Utama</span>
</a>

<a href="{{ route('receipts.index') }}"
   class="nav-link flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all relative group
          {{ request()->routeIs('receipts.*') ? 'nav-link-active text-blue-400' : 'text-slate-400 hover:text-white' }}">
    <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-all overflow-hidden"
          style="min-width:32px;min-height:32px;max-width:32px;max-height:32px;
                 {{ request()->routeIs('receipts.*') ? 'background:rgba(59,130,246,0.15);color:#60a5fa;' : 'background:rgba(255,255,255,0.05);' }}">
        <svg style="width:16px;height:16px;min-width:16px;min-height:16px;max-width:16px;max-height:16px;flex-shrink:0;display:block;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
    </span>
    <span class="font-display">Riwayat Struk</span>
</a>

<a href="{{ route('settings') }}"
   class="nav-link flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all relative group
          {{ request()->routeIs('settings') ? 'nav-link-active text-blue-400' : 'text-slate-400 hover:text-white' }}">
    <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-all overflow-hidden"
          style="min-width:32px;min-height:32px;max-width:32px;max-height:32px;
                 {{ request()->routeIs('settings') ? 'background:rgba(59,130,246,0.15);color:#60a5fa;' : 'background:rgba(255,255,255,0.05);' }}">
        <svg style="width:16px;height:16px;min-width:16px;min-height:16px;max-width:16px;max-height:16px;flex-shrink:0;display:block;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </span>
    <span class="font-display">Pengaturan</span>
</a>

<a href="{{ route('profile.edit') }}"
   class="nav-link flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all relative group
          {{ request()->routeIs('profile.edit') ? 'nav-link-active text-blue-400' : 'text-slate-400 hover:text-white' }}">
    <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-all overflow-hidden"
          style="min-width:32px;min-height:32px;max-width:32px;max-height:32px;
                 {{ request()->routeIs('profile.edit') ? 'background:rgba(59,130,246,0.15);color:#60a5fa;' : 'background:rgba(255,255,255,0.05);' }}">
        <svg style="width:16px;height:16px;min-width:16px;min-height:16px;max-width:16px;max-height:16px;flex-shrink:0;display:block;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
    </span>
    <span class="font-display">Profil Saya</span>
</a>
