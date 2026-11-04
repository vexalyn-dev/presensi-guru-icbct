<!-- Kelas yang menggunakan ruangan bersama -->
<div class="space-y-2">
    <label class="block text-sm font-semibold text-navy-800 dark:text-white">
        Kelas yang Menggunakan Ruangan Ini <span class="text-red-500">*</span>
    </label>

    <div class="relative">
        <div class="min-h-[52px] w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border-2 rounded-xl text-sm font-medium transition-all cursor-pointer flex items-center gap-2"
             :class="classDropdownOpen
                 ? 'border-navy-800 dark:border-gold-400 ring-2 ring-navy-800/10 dark:ring-gold-400/10'
                 : 'border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500'"
             @click.stop="classDropdownOpen = !classDropdownOpen"
             @keydown.enter.prevent="classDropdownOpen = !classDropdownOpen"
             @keydown.space.prevent="classDropdownOpen = !classDropdownOpen"
             role="button" tabindex="0">

            <div class="flex-1 min-w-0 flex flex-wrap items-center gap-1.5">
                <template x-for="cls in selectedClassrooms" :key="cls.id">
                    <span class="inline-flex items-center gap-1.5 pl-2.5 pr-1.5 py-1 rounded-lg bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 text-[11px] font-bold shadow-sm">
                        <span x-text="cls.name"></span>
                        <button type="button"
                                @click.stop="removeClassroom(cls.id)"
                                aria-label="Hapus kelas"
                                class="w-4 h-4 rounded-full bg-white/20 dark:bg-navy-900/15 flex items-center justify-center hover:bg-white/40 dark:hover:bg-navy-900/30 transition-colors">
                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </span>
                </template>

                <span x-show="selectedClassrooms.length === 0"
                      class="text-slate-400 dark:text-slate-500"
                      x-text="regularClasses.length ? 'Pilih kelas yang memakai ruangan ini...' : 'Belum ada kelas reguler'"></span>
            </div>

            <i data-lucide="chevron-down"
               class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0"
               :class="{'rotate-180': classDropdownOpen}"></i>
        </div>

        <div x-show="classDropdownOpen" x-cloak
             @click.outside="classDropdownOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
             class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden">

            <div class="p-2 border-b border-slate-100 dark:border-slate-700">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" x-model="classSearch" placeholder="Cari kelas..." autocomplete="off"
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent">
                </div>
            </div>

            <div class="max-h-64 overflow-y-auto p-1.5 space-y-0.5">
                <template x-for="cls in filteredClasses" :key="cls.id">
                    <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer transition-colors"
                           :class="selectedClassroomIds.includes(cls.id)
                               ? 'bg-slate-50 dark:bg-navy-900/20'
                               : 'hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                        <input type="checkbox"
                               :checked="selectedClassroomIds.includes(cls.id)"
                               @change="toggleClassroom(cls.id)"
                               class="w-4 h-4 rounded border-slate-300 text-navy-800 focus:ring-navy-800 dark:focus:ring-gold-500">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-navy-800 dark:text-white truncate" x-text="cls.name"></p>
                            <p class="text-[10px] text-slate-400 font-mono" x-text="cls.code"></p>
                        </div>
                        <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400 flex-shrink-0"
                           x-show="selectedClassroomIds.includes(cls.id)"></i>
                    </label>
                </template>

                <p x-show="filteredClasses.length === 0"
                   class="text-center text-xs text-slate-400 py-6"
                   x-text="regularClasses.length ? 'Kelas tidak ditemukan' : 'Belum ada kelas reguler. Tambah kelas reguler terlebih dahulu.'"></p>
            </div>
        </div>

        <template x-for="id in selectedClassroomIds" :key="'hidden-' + id">
            <input type="hidden" name="classroom_ids[]" :value="id">
        </template>
    </div>

    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
        Hanya kelas yang dipilih yang akan muncul di form scan ruangan ini.
    </p>

    @error('classroom_ids')
        <p class="text-xs font-medium text-red-500">{{ $message }}</p>
    @enderror
    @error('classroom_ids.*')
        <p class="text-xs font-medium text-red-500">{{ $message }}</p>
    @enderror
</div>
