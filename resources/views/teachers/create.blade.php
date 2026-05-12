@extends('layouts.app')

@section('page-title', 'Tambah Guru')

@section('content')
<div class="max-w-3xl mx-auto fade-in">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('teachers.index') }}"
            class="group flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600">
            <i data-lucide="arrow-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
            <span>Kembali</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Tambah Guru</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Daftarkan guru baru ke sistem</p>
        </div>
    </div>

    <div class="card p-6">
        <div class="mb-6 pb-4 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gold-100 dark:bg-gold-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-5 h-5 text-gold-600 dark:text-gold-400"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-navy-800 dark:text-white">Informasi Guru</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Lengkapi data guru dengan benar</p>
                </div>
            </div>
        </div>

        <form action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Photo Upload -->
            <div class="flex justify-center mb-6">
                <div class="text-center">
                    <div class="relative inline-block">
                        <div id="photoPreview" class="w-28 h-28 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center border-2 border-dashed border-slate-300 dark:border-slate-600 cursor-pointer hover:border-gold-400 transition-colors" onclick="document.getElementById('photo').click()">
                            <img id="previewImg" src="{{ asset('images/default-teacher.png') }}" class="w-28 h-28 rounded-full object-cover shadow-lg">
                            <div class="absolute inset-0 flex flex-col items-center justify-center opacity-0 hover:opacity-100 bg-black/20 rounded-full transition-opacity">
                                <i data-lucide="camera" class="w-8 h-8 text-white"></i>
                                <span class="text-[10px] text-white font-bold uppercase tracking-wider">Ubah Foto</span>
                            </div>
                        </div>
                    </div>
                    <input type="file" name="photo" id="photo" accept="image/*" class="hidden" onchange="previewPhoto(this)">
                    <p class="text-[10px] text-slate-500 mt-2">Max 2MB (JPG, PNG)</p>
                </div>
            </div>

            <!-- Name & Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Nama Lengkap
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="input-field @error('name') border-red-500 @enderror"
                           placeholder="Contoh: Budi Santoso, S.Pd">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="input-field @error('email') border-red-500 @enderror"
                           placeholder="guru@sekolah.sch.id">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Phone & Join Date -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        No. Telepon
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="input-field"
                           placeholder="08123456789">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Tanggal Bergabung
                    </label>
                    <input type="date" name="join_date" value="{{ old('join_date') }}"
                           class="input-field">
                </div>
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Alamat
                </label>
                <textarea name="address" rows="3"
                          class="input-field @error('address') border-red-500 @enderror"
                          placeholder="Alamat lengkap guru">{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bio -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Biodata
                </label>
                <textarea name="bio" rows="3"
                          class="input-field @error('bio') border-red-500 @enderror"
                          placeholder="Deskripsi singkat guru (opsional)">{{ old('bio') }}</textarea>
                @error('bio')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mata Pelajaran -->
            <div class="md:col-span-2 pt-4 border-t border-slate-200 dark:border-slate-700">
                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                    Mata Pelajaran <span class="text-xs font-normal text-slate-500">(Pilih satu mata pelajaran)</span>
                </label>
                <div x-data="subjectDropdown({{ Js::from($subjects) }}, {{ Js::from(old('subject')) }})" class="relative">
                    {{-- Hidden input for single subject --}}
                    <input type="hidden" name="subject" :value="selected.length > 0 ? selected[0] : ''">

                    {{-- The Standard-looking Trigger --}}
                    <div class="relative">
                        <i data-lucide="book-open" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 z-10 pointer-events-none"></i>
                        <div @click="open = !open" 
                             class="w-full pl-11 pr-10 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm cursor-pointer flex flex-wrap items-center gap-2 hover:border-blue-400 transition-all shadow-sm">
                            
                            <template x-if="selected.length === 0">
                                <span class="text-slate-400">Pilih mata pelajaran...</span>
                            </template>

                            <template x-if="selected.length > 0">
                                <div class="flex items-center gap-2 group/item animate-scale-in">
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-xl text-xs font-bold border border-blue-200 dark:border-blue-800"
                                          x-text="getSubject(selected[0]).name"></span>
                                    <button type="button" @click.stop="clear()" class="text-slate-400 hover:text-red-500 transition-colors">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </template>

                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                     class="text-slate-400 transition-all duration-300 transform" :class="open ? 'rotate-180' : ''">
                                    <path d="m6 9 6 6 6-6"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Floating Overlay Panel --}}
                    <div x-show="open" 
                         @click.outside="open = false"
                         class="absolute left-0 z-[100] w-full min-w-[300px] max-w-sm p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none origin-bottom"
                         style="bottom: 100%; top: auto; margin-bottom: 0.5rem;"
                         x-cloak>
                        <div class="max-h-60 overflow-y-auto space-y-1 pr-1 scrollbar-thin">
                            <template x-for="subject in options" :key="subject.id">
                                <div @click="select(subject.name)"
                                     class="flex items-center justify-between px-4 py-2.5 rounded-xl cursor-pointer transition-all border border-transparent"
                                     :class="isSelected(subject.name) ? 'bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300'">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-all bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700"
                                             :class="isSelected(subject.name) ? 'text-blue-600 border-blue-300' : 'text-slate-400'">
                                            <i data-lucide="book" class="w-3.5 h-3.5"></i>
                                        </div>
                                        <span class="text-sm tracking-tight" x-text="subject.name"></span>
                                    </div>
                                    <template x-if="isSelected(subject.name)">
                                        <i data-lucide="check" class="w-4 h-4 text-blue-600 animate-scale-in"></i>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Semester, Tahun Ajaran, Jam Per Minggu -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Semester
                    </label>
                    <select name="semester" class="input-field">
                        <option value="Ganjil" {{ old('semester') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ old('semester') === 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Tahun Ajaran
                    </label>
                    <input type="text" name="academic_year" value="{{ old('academic_year', '2024/2025') }}"
                           class="input-field"
                           placeholder="2024/2025">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Jam Per Minggu
                    </label>
                    <input type="number" name="hours_per_week" value="{{ old('hours_per_week', 2) }}" min="1" max="20"
                           class="input-field">
                </div>
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Password
                    </label>
                    <input type="password" name="password" required
                           class="input-field @error('password') border-red-500 @enderror"
                           placeholder="••••••••">
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Konfirmasi Password
                    </label>
                    <input type="password" name="password_confirmation" required
                           class="input-field"
                           placeholder="••••••••">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('teachers.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="btn-ripple btn-primary">
                    Simpan Guru
                </button>
            </div>
        </form>
    </div>
</div>

    <script>
        /** 
         * Define components globally for maximum reliability 
         */
        window.subjectDropdown = (options, initialSelected) => {
            return {
                open: false,
                options: Array.isArray(options) ? options : Object.values(options),
                selected: initialSelected ? [String(initialSelected)] : [],

                init() {
                    const uniqueNames = new Set();
                    this.options = this.options.filter(opt => {
                        const nameKey = String(opt.name).trim().toUpperCase();
                        if (uniqueNames.has(nameKey)) return false;
                        uniqueNames.add(nameKey);
                        return true;
                    });
                    
                    return; // Avoid u is not a function
                },

                select(name) {
                    this.selected = [String(name)];
                    this.open = false;
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                },

                clear() {
                    this.selected = [];
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                },

                isSelected(name) {
                    return this.selected.includes(String(name));
                },

                getSubject(name) {
                    const strName = String(name);
                    return this.options.find(opt => String(opt.name) === strName) || { name: strName || 'Unknown' };
                }
            };
        };

        function previewPhoto(input) {
            const preview = document.getElementById('photoPreview');
            const img = document.getElementById('previewImg');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.classList.add('opacity-50');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar */
        .scrollbar-thin::-webkit-scrollbar { width: 4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { 
            background: #cbd5e1; 
            border-radius: 10px; 
        }
        .dark .scrollbar-thin::-webkit-scrollbar-thumb { background: #334155; }
    </style>
@endsection