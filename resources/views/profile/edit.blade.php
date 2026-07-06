<x-app-layout>
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 md:mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-black tracking-tight font-display text-slate-900 dark:text-white">
                Profil Sistem</h1>
            <p class="text-xs md:text-sm text-slate-500 mt-1">Kelola data diri, perbarui kata sandi, dan kelola keamanan
                akun dasbor Anda.</p>
        </div>

        <!-- Easy Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar (Logout)
            </button>
        </form>
    </div>

    <div class="space-y-6 max-w-4xl">
        <!-- Card: Update Profile Information -->
        <div class="pro-card p-6 md:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Card: Update Password -->
        <div class="pro-card p-6 md:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Card: Delete Account -->
        <div
            class="pro-card p-6 md:p-8 border border-transparent hover:border-red-200 dark:hover:border-red-500/30 transition-colors">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>