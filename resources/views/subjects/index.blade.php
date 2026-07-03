@extends('layouts.app')

@section('page-title', 'Mata Pelajaran')

@section('content')
    <div x-data="subjectsApp()">
    <div class="fade-in space-y-6">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Mata Pelajaran</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Kelola data mata pelajaran</p>
            </div>
            <a href="{{ route('subjects.create') }}" class="btn-primary flex items-center gap-2 w-fit">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Mapel
            </a>
        </div>

        <!-- Filters -->
        <div class="card p-5">
            <form @submit.prevent="filterSubjects" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[250px]">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Cari</label>
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                        <input type="text" 
                               x-model="search" 
                               @input.debounce.300ms="filterSubjects"
                               placeholder="Cari nama mapel atau guru..."
                               class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800">
                    </div>
                </div>

                <!-- Modern Status Dropdown -->
                <div class="w-full sm:w-48" x-data="{ open: false }" @click.outside="open = false">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Status</label>
                    <div class="relative">
                        <button type="button" 
                                @click="open = !open"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 flex items-center justify-between hover:border-navy-300 dark:hover:border-navy-600 transition-colors">
                            <span x-text="getStatusText()">Semua</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                             class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden"
                             x-cloak>
                            <button type="button" 
                                    @click="selectStatus(''); open = false; filterSubjects()"
                                    class="w-full px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2"
                                    :class="status === '' ? 'bg-navy-50 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 font-semibold' : 'text-slate-700 dark:text-slate-300'">
                                <i data-lucide="list" class="w-4 h-4" x-show="status === ''"></i>
                                Semua
                            </button>
                            <button type="button" 
                                    @click="selectStatus('active'); open = false; filterSubjects()"
                                    class="w-full px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2"
                                    :class="status === 'active' ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 font-semibold' : 'text-slate-700 dark:text-slate-300'">
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-500" x-show="status === 'active'"></i>
                                <span class="w-2 h-2 bg-green-500 rounded-full" x-show="status !== 'active'"></span>
                                Aktif
                            </button>
                            <button type="button" 
                                    @click="selectStatus('inactive'); open = false; filterSubjects()"
                                    class="w-full px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2"
                                    :class="status === 'inactive' ? 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold' : 'text-slate-700 dark:text-slate-300'">
                                <i data-lucide="x-circle" class="w-4 h-4 text-slate-500" x-show="status === 'inactive'"></i>
                                <span class="w-2 h-2 bg-slate-400 rounded-full" x-show="status !== 'inactive'"></span>
                                Nonaktif
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="submit" 
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-navy-800 hover:bg-navy-900 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        Filter
                    </button>
                    <button type="button" 
                            @click="resetFilters"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-colors text-center flex items-center gap-2">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Reset
                    </button>
                </div>
            </form>
        </div>

        <!-- Loading Indicator -->
        <div x-show="loading" class="flex justify-center py-12" x-cloak>
            <div class="flex items-center gap-3">
                <div class="w-6 h-6 border-2 border-navy-800 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-sm text-slate-600 dark:text-slate-400">Memuat data...</span>
            </div>
        </div>

        <!-- Subjects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" x-show="!loading">
            <template x-for="subject in subjects" :key="subject.id">
                <div class="card p-5 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center">
                            <i data-lucide="book-open" class="w-6 h-6 text-white dark:text-navy-900"></i>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                              :class="subject.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-slate-100 text-slate-700'">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="subject.is_active ? 'bg-green-500' : 'bg-slate-400'"></span>
                            <span x-text="subject.is_active ? 'Aktif' : 'Nonaktif'"></span>
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-1" x-text="subject.name"></h3>

                    <template x-if="subject.teachers && subject.teachers.length > 0">
                        <div class="flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 font-semibold mb-3 bg-blue-50 dark:bg-blue-950/30 px-3 py-1.5 rounded-xl w-fit">
                            <i data-lucide="user" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                            <span x-text="subject.teachers.map(t => t.name).join(', ')"></span>
                        </div>
                    </template>
                    <template x-if="!subject.teachers || subject.teachers.length === 0">
                        <div class="flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400 font-medium mb-3 bg-amber-50 dark:bg-amber-950/20 px-3 py-1.5 rounded-xl w-fit">
                            <i data-lucide="user-x" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
                            <span class="italic">Mata pelajaran ini belum ada gurunya</span>
                        </div>
                    </template>

                    <template x-if="subject.description">
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mb-4 h-8" x-text="subject.description"></p>
                    </template>
                    <template x-if="!subject.description">
                        <div class="mb-4 h-8 text-xs text-slate-400 italic">Tidak ada deskripsi</div>
                    </template>

                    <div class="flex items-center gap-2 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <a :href="`/subjects/${subject.id}/edit`"
                           class="flex-1 px-3 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition-colors text-center flex items-center justify-center gap-2">
                            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                            Edit
                        </a>
                        <button type="button"
                                @click="confirmDelete(subject.id, subject.name)"
                                class="w-full px-3 py-2.5 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl text-xs font-semibold transition-colors flex items-center justify-center gap-2">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            Hapus
                        </button>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="subjects.length === 0 && !loading">
                <div class="col-span-full">
                    <div class="card p-16 text-center">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                                <i data-lucide="book-open" class="w-10 h-10 text-slate-400"></i>
                            </div>
                            <div>
                                <p class="text-slate-500 dark:text-slate-400 font-medium" x-text="search || status ? 'Tidak ada hasil ditemukan' : 'Belum ada mata pelajaran'"></p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1" x-text="search || status ? 'Coba ubah filter pencarian' : 'Mulai dengan menambahkan mapel pertama'"></p>
                            </div>
                            <template x-if="!search && !status">
                                <a href="{{ route('subjects.create') }}" class="btn-primary flex items-center gap-2 text-sm">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                    Tambah Mapel
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Pagination -->
        <div x-show="pagination && pagination.links && pagination.links.length > 3" class="flex justify-center" x-cloak>
            <nav class="flex items-center gap-1">
                <!-- Previous -->
                <template x-if="pagination.prev_page_url">
                    <button @click="loadPage(pagination.prev_page_url)" 
                            class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        ←
                    </button>
                </template>

                <!-- Page Numbers -->
                <template x-for="link in pagination.links" :key="link.label">
                    <template x-if="link.url">
                        <button @click="loadPage(link.url)"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                :class="link.active 
                                    ? 'bg-navy-800 text-white dark:bg-gold-500 dark:text-navy-900' 
                                    : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                x-text="link.label"></button>
                    </template>
                </template>

                <!-- Next -->
                <template x-if="pagination.next_page_url">
                    <button @click="loadPage(pagination.next_page_url)" 
                            class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        →
                    </button>
                </template>
            </nav>
        </div>
    </div>{{-- end fade-in --}}

        <!-- Delete Confirmation Modal -->
    <div x-show="deleteModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="position: fixed; top: 0; left: 0; right: 0; bottom: 0;"
         x-cloak>
        <div @click.outside="deleteModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-700 relative z-10">

            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-1">Konfirmasi Hapus</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Apakah Anda yakin ingin menghapus <span class="font-semibold text-navy-800 dark:text-white" x-text="deleteSubjectName"></span>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button @click="deleteModal = false"
                        class="px-5 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-colors">
                    Batal
                </button>
                <button @click="deleteSubject"
                        class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>{{-- end deleteModal --}}

        <!-- Toast Notification -->
        <div x-show="toast.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="fixed top-6 right-6 z-[110] flex items-center gap-3 px-5 py-4 rounded-xl shadow-2xl"
             :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
             x-cloak>
            <i :data-lucide="toast.type === 'success' ? 'check-circle' : 'alert-circle'" class="w-5 h-5"></i>
            <p class="text-sm font-medium" x-text="toast.message"></p>
        </div>

    </div>{{-- end x-data="subjectsApp()" --}}

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('subjectsApp', () => ({
                subjects: @json($subjects->items() ?? []),
                pagination: @json($subjects ?? null),
                search: '',
                status: '',
                loading: false,
                deleteModal: false,
                deleteSubjectId: null,
                deleteSubjectName: '',
                toast: { show: false, message: '', type: 'success' },

                init() {
                    if (window.lucide) lucide.createIcons();
                },

                getStatusText() {
                    if (this.status === '') return 'Semua';
                    if (this.status === 'active') return 'Aktif';
                    return 'Nonaktif';
                },

                selectStatus(value) {
                    this.status = value;
                },

                resetFilters() {
                    this.search = '';
                    this.status = '';
                    this.filterSubjects();
                },

                async filterSubjects() {
                    this.loading = true;
                    try {
                        const response = await fetch(`/subjects?search=${this.search}&status=${this.status}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        this.subjects = data.data;
                        this.pagination = data;
                    } catch (error) {
                        console.error('Filter error:', error);
                        this.showToast('Gagal memuat data', 'error');
                    } finally {
                        this.loading = false;
                        if (window.lucide) lucide.createIcons();
                    }
                },

                async loadPage(url) {
                    this.loading = true;
                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        this.subjects = data.data;
                        this.pagination = data;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } catch (error) {
                        console.error('Pagination error:', error);
                        this.showToast('Gagal memuat halaman', 'error');
                    } finally {
                        this.loading = false;
                        if (window.lucide) lucide.createIcons();
                    }
                },

                confirmDelete(id, name) {
                    this.deleteSubjectId = id;
                    this.deleteSubjectName = name;
                    this.deleteModal = true;
                },

                async deleteSubject() {
                    try {
                        const response = await fetch(`/subjects/${this.deleteSubjectId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            this.subjects = this.subjects.filter(s => s.id !== this.deleteSubjectId);
                            this.showToast('Mata pelajaran berhasil dihapus!', 'success');
                        } else {
                            const error = await response.json();
                            this.showToast(error.message || 'Gagal menghapus mata pelajaran', 'error');
                        }
                    } catch (error) {
                        console.error('Delete error:', error);
                        this.showToast('Terjadi kesalahan saat menghapus', 'error');
                    } finally {
                        this.deleteModal = false;
                        this.deleteSubjectId = null;
                        if (window.lucide) lucide.createIcons();
                    }
                },

                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 4000);
                }
            }));
        });

        // Init icons on load
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>

    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        [x-cloak] { display: none !important; }
    </style>
@endsection