<x-app-layout>
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Profil Admin</h1>
        <p class="text-sm text-slate-400 mt-1">Kelola data diri, perbarui kata sandi, dan kelola keamanan akun dasbor Anda.</p>
    </div>

    <div class="space-y-6 max-w-4xl">
        <!-- Card: Update Profile Information -->
        <div class="glass-card rounded-2xl p-6 md:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Card: Update Password -->
        <div class="glass-card rounded-2xl p-6 md:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Card: Delete Account -->
        <div class="glass-card rounded-2xl p-6 md:p-8 border border-red-500/10 hover:border-red-500/20">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
