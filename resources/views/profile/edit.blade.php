<x-app-layout>
    <!-- Header Section -->
    <div class="mb-6 md:mb-8">
        <h1 class="text-2xl md:text-3xl font-black tracking-tight font-display text-slate-900 dark:text-white">Profil Admin</h1>
        <p class="text-xs md:text-sm text-slate-500 mt-1">Kelola data diri, perbarui kata sandi, dan kelola keamanan akun dasbor Anda.</p>
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
        <div class="pro-card p-6 md:p-8 border border-transparent hover:border-red-200 dark:hover:border-red-500/30 transition-colors">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
