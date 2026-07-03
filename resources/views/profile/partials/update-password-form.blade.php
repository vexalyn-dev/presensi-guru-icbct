<section class="card p-6" x-data="passwordForm()">
    <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
        <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
            <i data-lucide="lock" class="w-5 h-5 text-white dark:text-navy-900"></i>
        </div>
        <div>
            <h2 class="text-base font-bold text-navy-800 dark:text-white">Keamanan Akun</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Ubah password secara berkala</p>
        </div>
    </div>

    @if (session('status') === 'password-updated')
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-center gap-3 animate-slide-up">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
            <p class="text-sm font-medium text-green-800 dark:text-green-300">Password berhasil diperbarui!</p>
        </div>
    @endif

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Current Password -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Password Saat Ini</label>
                <div class="relative group">
                    <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="password" name="current_password" id="update_password_current_password" required autocomplete="current-password"
                           class="w-full pl-11 pr-11 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                    <button type="button" @click="toggleVisibility('current')" class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        <svg x-show="!visibility.current" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg x-show="visibility.current" class="w-4 h-4 text-navy-600 dark:text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                </div>
                @error('current_password', 'updatePassword')
                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Password Baru</label>
                <div class="relative group">
                    <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="password" name="password" id="update_password_password" required autocomplete="new-password" x-model="newPassword" @input="checkStrength"
                           class="w-full pl-11 pr-11 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                    <button type="button" @click="toggleVisibility('new')" class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        <svg x-show="!visibility.new" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg x-show="visibility.new" class="w-4 h-4 text-navy-600 dark:text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                </div>
                <div class="mt-2 flex items-center gap-2" x-show="newPassword.length > 0">
                    <div class="flex-1 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-300" :class="strengthClass" :style="`width: ${strengthPercent}%`"></div>
                    </div>
                    <span class="text-[10px] font-semibold" :class="strengthTextColor" x-text="strengthText"></span>
                </div>
                @error('password', 'updatePassword')
                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Konfirmasi Password</label>
                <div class="relative group">
                    <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="password" name="password_confirmation" id="update_password_password_confirmation" required autocomplete="new-password" x-model="confirmPassword"
                           class="w-full pl-11 pr-11 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500"
                           :class="confirmPassword && confirmPassword !== newPassword ? 'border-red-500 ring-red-500' : ''">
                    <button type="button" @click="toggleVisibility('confirm')" class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        <svg x-show="!visibility.confirm" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg x-show="visibility.confirm" class="w-4 h-4 text-navy-600 dark:text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                </div>
                <p class="mt-2 text-xs" x-show="confirmPassword.length > 0" :class="confirmPassword === newPassword ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                    <i :data-lucide="confirmPassword === newPassword ? 'check-circle' : 'x-circle'" class="w-3 h-3 inline mr-1"></i>
                    <span x-text="confirmPassword === newPassword ? 'Password cocok' : 'Password tidak cocok'"></span>
                </p>
                @error('password_confirmation', 'updatePassword')
                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end pt-5 border-t border-slate-200 dark:border-slate-700">
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:shadow-navy-800/40 dark:hover:shadow-gold-400/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                Perbarui Keamanan
            </button>
        </div>
    </form>
</section>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('passwordForm', () => ({
            newPassword: '',
            confirmPassword: '',
            visibility: { current: false, new: false, confirm: false },
            strength: 0,
            
            toggleVisibility(field) {
                this.visibility[field] = !this.visibility[field];
            },
            
            get strengthClass() {
                return ['bg-slate-300', 'bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'][this.strength] || 'bg-slate-300';
            },
            get strengthTextColor() {
                return ['text-slate-400', 'text-red-600', 'text-orange-600', 'text-yellow-600', 'text-blue-600', 'text-green-600'][this.strength] || 'text-slate-400';
            },
            get strengthText() {
                return ['-', 'Lemah', 'Cukup', 'Bagus', 'Kuat', 'Sangat Kuat'][this.strength] || '-';
            },
            get strengthPercent() {
                return this.strength * 20;
            },
            
            checkStrength() {
                let score = 0;
                const p = this.newPassword;
                if (p.length >= 8) score++;
                if (p.length >= 12) score++;
                if (/[a-z]/.test(p) && /[A-Z]/.test(p)) score++;
                if (/[0-9]/.test(p)) score++;
                if (/[^a-zA-Z0-9]/.test(p)) score++;
                this.strength = Math.min(score, 5);
            }
        }));
    });
</script>