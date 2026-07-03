<section class="card p-6 border-l-4 border-red-500">
    <div class="flex items-start gap-4">
        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
        </div>
        <div class="flex-1">
            <h2 class="text-base font-bold text-red-700 dark:text-red-400 mb-1">Hapus Akun</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Setelah akun dihapus, semua data tidak dapat dipulihkan. Pastikan Anda telah mencadangkan data penting.
            </p>
            
            <button x-data="" 
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                    class="px-5 py-2.5 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl text-sm font-semibold transition-all flex items-center gap-2">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Hapus Akun Permanen
            </button>
        </div>
    </div>
</section>

<!-- Confirmation Modal -->
<x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
    <div class="card p-6 max-w-md mx-auto">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-1">Konfirmasi Hapus Akun</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Masukkan password Anda untuk konfirmasi penghapusan permanen.
                </p>
            </div>
        </div>
        
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('delete')

            <div>
                <label for="password" class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Password</label>
                <input type="password" name="password" id="password" 
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="••••••••">
                @error('password', 'userDeletion')
                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <button type="button" x-on:click="$dispatch('close')"
                        class="px-5 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</x-modal>