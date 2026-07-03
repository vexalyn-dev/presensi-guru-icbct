<section class="card p-6">
    <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
        <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
            <i data-lucide="user" class="w-5 h-5 text-white dark:text-navy-900"></i>
        </div>
        <div>
            <h2 class="text-base font-bold text-navy-800 dark:text-white">Informasi Profil</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Perbarui informasi akun dan email Anda</p>
        </div>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-center gap-3 animate-slide-up">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
            <p class="text-sm font-medium text-green-800 dark:text-green-300">Profil berhasil diperbarui!</p>
        </div>
    @endif

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Name -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                <div class="relative group">
                    <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                           class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                </div>
                @error('name')
                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Email <span class="text-red-500">*</span></label>
                <div class="relative group">
                    <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                           class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                @enderror
                
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            Email Anda belum terverifikasi. 
                            <button form="send-verification" class="underline font-medium hover:text-blue-900 dark:hover:text-blue-200">Kirim ulang email verifikasi</button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-xs font-medium text-green-600 dark:text-green-400">✓ Email verifikasi telah dikirim!</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="flex justify-end pt-5 border-t border-slate-200 dark:border-slate-700">
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:shadow-navy-800/40 dark:hover:shadow-gold-400/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>
</section>

<!-- Hidden verification form -->
<form id="send-verification" method="post" action="{{ route('verification.send') }}" class="hidden">@csrf</form>