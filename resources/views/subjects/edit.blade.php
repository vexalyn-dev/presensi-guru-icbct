@extends('layouts.app')

@section('page-title', 'Edit Mata Pelajaran')

@section('content')
    <div class="fade-in" x-data="editSubject()">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('subjects.index') }}" 
                   class="group flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600">
                    <i data-lucide="arrow-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
                    <span>Kembali</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Edit Mata Pelajaran</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Perbarui informasi mata pelajaran</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            <!-- Left: Subject Info Card -->
            <div class="lg:col-span-1">
                <div class="card p-6 sticky top-6">
                    <!-- Icon & Title -->
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="relative mb-4">
                            <div class="w-24 h-24 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-3xl flex items-center justify-center shadow-2xl shadow-navy-800/30 dark:shadow-gold-400/30 transform transition-transform hover:scale-105 hover:rotate-3">
                                <i data-lucide="book-open" class="w-12 h-12 text-white dark:text-navy-900"></i>
                            </div>
                            <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-white dark:border-slate-800 flex items-center justify-center"
                                 :class="isActive ? 'animate-pulse' : ''">
                                <i data-lucide="check" class="w-4 h-4 text-white" x-show="isActive"></i>
                            </div>
                        </div>
                        <h2 class="text-lg font-bold text-navy-800 dark:text-white mb-1" x-text="subjectName || '{{ $subject->name }}'">{{ $subject->name }}</h2>
                    </div>

                    <!-- Status Badge -->
                    <div class="p-4 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700/50 dark:to-slate-700 rounded-2xl mb-4 border border-slate-200 dark:border-slate-600">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3 font-semibold">Status Saat Ini</p>
                        <div class="flex items-center justify-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                                  :class="isActive ? 'bg-green-500 animate-pulse shadow-lg shadow-green-500/50' : 'bg-slate-400'"></span>
                            <span class="text-sm font-bold transition-colors duration-300"
                                  :class="isActive ? 'text-green-600 dark:text-green-400' : 'text-slate-600 dark:text-slate-400'"
                                  x-text="isActive ? 'Aktif' : 'Nonaktif'">
                                {{ $subject->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                                Pastikan nama mata pelajaran jelas dan mudah dipahami.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Edit Form (Full Width - 3/4) -->
            <div class="lg:col-span-3 space-y-6">

                <!-- Main Form Card -->
                <div class="card p-8">
                    <div class="flex items-center gap-3 mb-8 pb-6 border-b border-slate-200 dark:border-slate-700">
                        <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                            <i data-lucide="edit-2" class="w-6 h-6 text-white dark:text-navy-900"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-navy-800 dark:text-white">Informasi Mata Pelajaran</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Lengkapi semua field di bawah ini</p>
                        </div>
                    </div>

                    <form action="{{ route('subjects.update', $subject) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Nama Mapel -->
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                    Nama Mata Pelajaran <span class="text-red-500">*</span>
                                </label>
                                <div class="relative group">
                                    <i data-lucide="book-open" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                    <input type="text" 
                                           name="name" 
                                           value="{{ old('name', $subject->name) }}" 
                                           required
                                           x-model="subjectName"
                                           @input="updateSubjectName"
                                           class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-base font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600"
                                           placeholder="Contoh: Matematika Wajib">
                                </div>
                                @error('name')
                                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1 animate-shake">
                                        <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Guru Pengampu & Status in 2 columns -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Multi-Select Teacher with Checkboxes -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Guru Pengampu
                                        <span class="text-slate-400 font-normal">(Dapat pilih lebih dari 1)</span>
                                    </label>
                                    <div class="relative" x-data="{ 
                                        open: false, 
                                        search: '', 
                                        selected: @json($selectedTeacherIds ?? [])
                                    }"
                                         @click.outside="open = false">

                                        <!-- Dropdown Trigger -->
                                        <div @click="open = !open" 
                                             class="relative group cursor-pointer">
                                            <div class="absolute left-4 top-1/2 -translate-y-1/2">
                                                <i data-lucide="users" class="w-5 h-5 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                            </div>
                                            <div class="w-full pl-12 pr-12 py-4 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all hover:border-navy-300 dark:hover:border-gold-600 flex items-center justify-between min-h-[56px]"
                                                 :class="{'ring-2 ring-navy-800 dark:ring-gold-500 border-navy-500 dark:border-gold-500': open}">
                                                <span class="truncate" 
                                                      x-text="selected.length > 0 ? selected.length + ' guru dipilih' : 'Pilih Guru Pengampu'">
                                                    {{ $subject->teachers->count() > 0 ? $subject->teachers->count() . ' guru dipilih' : 'Pilih Guru Pengampu' }}
                                                </span>
                                                <i data-lucide="chevron-down" 
                                                   class="w-5 h-5 text-slate-400 transition-transform duration-200"
                                                   :class="{'rotate-180': open}"></i>
                                            </div>
                                        </div>

                                        <!-- Dropdown Menu -->
                                        <div x-show="open" 
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                             class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl overflow-hidden"
                                             x-cloak>

                                            <!-- Search Input -->
                                            <div class="p-3 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50">
                                                <div class="relative">
                                                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                                    <input type="text" 
                                                           x-model="search" 
                                                           placeholder="Cari guru..." 
                                                           class="w-full pl-10 pr-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                                                </div>
                                            </div>

                                            <!-- Teachers List with Checkboxes -->
                                            <div class="max-h-60 overflow-y-auto p-2">
                                                <template x-for="teacher in filteredTeachers" :key="teacher.id">
                                                    <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer transition-all duration-200 hover:bg-navy-50 dark:hover:bg-navy-900/20"
                                                           :class="selected.includes(teacher.id) ? 'bg-navy-50 dark:bg-navy-900/20 border border-navy-200 dark:border-navy-800' : ''">
                                                        
                                                        <!-- Checkbox -->
                                                        <input type="checkbox" 
                                                               :value="teacher.id"
                                                               x-model="selected"
                                                               class="w-5 h-5 rounded border-slate-300 text-navy-600 focus:ring-navy-500 cursor-pointer">
                                                        
                                                        <!-- Avatar -->
                                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center text-white dark:text-navy-900 font-bold text-sm flex-shrink-0">
                                                            <span x-text="teacher.name.charAt(0).toUpperCase()"></span>
                                                        </div>
                                                        
                                                        <!-- Teacher Info -->
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate" x-text="teacher.name"></p>
                                                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate" x-text="teacher.email"></p>
                                                        </div>
                                                    </label>
                                                </template>

                                                <!-- No Results -->
                                                <div x-show="filteredTeachers.length === 0" 
                                                     class="p-4 text-center text-slate-500 dark:text-slate-400 text-sm">
                                                    <i data-lucide="user-x" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                                    <p>Tidak ada guru ditemukan</p>
                                                </div>
                                            </div>
                                            
                                            <!-- Selected Count Footer -->
                                            <div x-show="selected.length > 0" 
                                                 class="p-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-xs text-slate-600 dark:text-slate-400">
                                                        <span class="font-bold" x-text="selected.length"></span> guru dipilih
                                                    </p>
                                                    <button type="button" 
                                                            @click="selected = []"
                                                            class="text-xs text-red-600 dark:text-red-400 hover:underline">
                                                        Hapus semua
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Hidden Inputs for Selected Teachers -->
                                        <template x-for="teacherId in selected" :key="teacherId">
                                            <input type="hidden" name="teacher_ids[]" :value="teacherId">
                                        </template>
                                    </div>
                                    @error('teacher_ids')
                                        <p class="mt-2 text-xs text-red-500 flex items-center gap-1 animate-shake">
                                            <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Modern Toggle Switch -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Status
                                    </label>
                                    <div class="relative">
                                        <label class="flex items-center justify-between p-4 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700/50 dark:to-slate-700 rounded-xl cursor-pointer border-2 transition-all duration-300 hover:shadow-md"
                                               :class="isActive ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-slate-200 dark:border-slate-600'">
                                            <input type="checkbox" 
                                                   name="is_active" 
                                                   value="1" 
                                                   {{ old('is_active', $subject->is_active) ? 'checked' : '' }}
                                                   x-model="isActive"
                                                   class="sr-only"
                                                   @change="toggleStatus">

                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-300"
                                                     :class="isActive ? 'bg-green-500 shadow-lg shadow-green-500/30' : 'bg-slate-300 dark:bg-slate-600'">
                                                    <i data-lucide="check" class="w-6 h-6 text-white" x-show="isActive" x-transition></i>
                                                    <i data-lucide="x" class="w-6 h-6 text-slate-500 dark:text-slate-400" x-show="!isActive" x-transition></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-bold transition-colors duration-300"
                                                       :class="isActive ? 'text-green-700 dark:text-green-400' : 'text-slate-600 dark:text-slate-400'"
                                                       x-text="isActive ? 'Aktif' : 'Nonaktif'">
                                                        {{ $subject->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </p>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400">Dapat digunakan</p>
                                                </div>
                                            </div>

                                            <!-- Toggle Switch Visual -->
                                            <div class="relative w-16 h-9 transition-all duration-300"
                                                 :class="isActive ? 'opacity-100' : 'opacity-70'">
                                                <div class="absolute inset-0 rounded-full transition-colors duration-300"
                                                     :class="isActive ? 'bg-green-500' : 'bg-slate-300 dark:bg-slate-600'"></div>
                                                <div class="absolute top-1 left-1 w-7 h-7 bg-white rounded-full shadow-md transition-all duration-300 transform"
                                                     :class="isActive ? 'translate-x-7' : 'translate-x-0'"></div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                    Deskripsi
                                    <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <div class="relative group">
                                    <i data-lucide="align-left" class="absolute left-4 top-4 w-5 h-5 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                    <textarea name="description" 
                                              rows="4"
                                              class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all resize-none hover:border-navy-300 dark:hover:border-gold-600"
                                              placeholder="Deskripsi singkat tentang mata pelajaran ini...">{{ old('description', $subject->description) }}</textarea>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Maksimal 500 karakter</p>
                                @error('description')
                                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1 animate-shake">
                                        <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-4 mt-10 pt-8 border-t border-slate-200 dark:border-slate-700">
                            <button type="submit" 
                                    class="flex-1 md:flex-none px-10 py-4 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:shadow-navy-800/40 dark:hover:shadow-gold-400/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center justify-center gap-3">
                                <i data-lucide="save" class="w-5 h-5"></i>
                                Perbarui Mapel
                            </button>
                            <a href="{{ route('subjects.index') }}" 
                               class="px-8 py-4 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-bold transition-all hover:-translate-y-0.5 active:translate-y-0">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Alpine.js Component
        document.addEventListener('alpine:init', () => {
            Alpine.data('editSubject', () => ({
                subjectName: '{{ $subject->name }}',
                isActive: {{ $subject->is_active ? 'true' : 'false' }},
                teachers: @json($teachers ?? []),

                updateSubjectName() {
                    // Real-time update logic if needed
                },

                toggleStatus() {
                    // Status toggle animation
                },

                get filteredTeachers() {
                    if (!this.search) return this.teachers;
                    return this.teachers.filter(t => 
                        t.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        t.email.toLowerCase().includes(this.search.toLowerCase())
                    );
                }
            }));
        });

        // Init Icons
        document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
    </script>

    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .animate-shake {
            animation: shake 0.3s ease-in-out;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Custom Scrollbar for Dropdown */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }
        .overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }
        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .dark .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #475569;
        }
        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endsection