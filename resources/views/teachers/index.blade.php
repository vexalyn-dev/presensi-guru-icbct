@extends('layouts.app')

@section('page-title', 'Data Guru')

@section('content')
<div class="space-y-6 fade-in">
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Guru</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['total'] ?? 0 }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">ID otomatis</p>
                </div>
            </div>
        </div>
        
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="circle-check" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Aktif</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['active'] ?? 0 }}</h3>
                    <p class="text-[10px] text-green-500 mt-1">{{ ($stats['total'] ?? 0) > 0 ? round((($stats['active'] ?? 0)/($stats['total'] ?? 1))*100) : 0 }}% dari total</p>
                </div>
            </div>
        </div>
        
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="circle-x" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Nonaktif</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['inactive'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gold-50 dark:bg-gold-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="scan-face" class="w-6 h-6 text-gold-600 dark:text-gold-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Hadir Hari Ini</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['today_checkin'] ?? 0 }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">{{ now()->locale('id')->format('d M') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <form action="{{ route('teachers.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <div class="relative flex-1 lg:w-64">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, telepon..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-navy-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-400">
            </div>
            <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 bg-white dark:bg-navy-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-400">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </form>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('teachers.create') }}" class="btn-ripple btn-primary flex items-center gap-2 text-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Guru
            </a>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div id="bulkActions" class="hidden card p-4 bg-gold-50 dark:bg-gold-900/20 border border-gold-200 dark:border-gold-800">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gold-800 dark:text-gold-300">
                <span id="selectedCount">0</span> guru dipilih
            </p>
            <div class="flex items-center gap-2">
                <button onclick="bulkToggleStatus(true)" class="px-3 py-1.5 bg-green-500 text-white text-xs font-medium rounded-lg hover:bg-green-600 transition-colors">Aktifkan</button>
                <button onclick="bulkToggleStatus(false)" class="px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded-lg hover:bg-red-600 transition-colors">Nonaktifkan</button>
                <button onclick="triggerBulkDelete()" class="px-3 py-1.5 bg-slate-700 text-white text-xs font-medium rounded-lg hover:bg-slate-800 transition-colors">Hapus</button>
                <button onclick="clearSelection()" class="px-3 py-1.5 bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-lg hover:bg-slate-300 transition-colors">Batal</button>
            </div>
        </div>
    </div>

    <!-- Teachers Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="teachersTable">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-3 py-3 w-10">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 rounded border-slate-300 text-navy-800 focus:ring-navy-800">
                        </th>
                        <th class="px-3 py-3 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase">ID</th>
                        <th class="px-3 py-3 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Guru</th>
                        <th class="px-3 py-3 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Email</th>
                        <th class="px-3 py-3 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Telepon</th>
                        <th class="px-3 py-3 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Status</th>
                        <th class="px-3 py-3 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Mapel</th>
                        <th class="px-3 py-3 text-center text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($teachers as $teacher)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-all group/row">
                            <td class="px-3 py-3 text-center">
                                <input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}" 
                                       class="teacher-checkbox w-4 h-4 rounded border-slate-300 text-navy-800 focus:ring-navy-800"
                                       onchange="updateBulkActions()">
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-xs font-mono text-slate-400">#{{ str_pad($teacher->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="relative group/avatar">
                                        <img src="{{ $teacher->photo_url }}" 
                                             class="w-12 h-12 rounded-2xl object-cover border-2 border-white dark:border-slate-700 shadow-sm group-hover/avatar:scale-105 transition-transform duration-300">
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-slate-800 rounded-full shadow-sm"></div>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-navy-800 dark:text-white group-hover/row:text-blue-600 transition-colors">{{ $teacher->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-medium tracking-tight">ID: {{ $teacher->formatted_id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-xs text-slate-600 dark:text-slate-300">{{ $teacher->email }}</td>
                            <td class="px-3 py-3 text-xs text-slate-600 dark:text-slate-300">{{ $teacher->phone ?? '-' }}</td>
                            <td class="px-3 py-3">
                                <form action="{{ route('teachers.toggle-status', $teacher) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-medium transition-colors
                                        {{ $teacher->is_active 
                                            ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 hover:bg-green-200' 
                                            : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400 hover:bg-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $teacher->is_active ? 'bg-green-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                        {{ $teacher->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-3 py-3">
                                @if($teacher->subject)
                                    <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg text-[10px] font-bold border border-blue-100 dark:border-blue-800 uppercase tracking-wider">
                                        {{ $teacher->subject }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum diatur</span>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('teachers.show', $teacher) }}" class="p-1.5 bg-slate-100 dark:bg-slate-700 rounded-lg transition-colors hover:bg-blue-100 dark:hover:bg-blue-900/30" title="Lihat Detail">
                                        <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-600 dark:text-slate-300"></i>
                                    </a>
                                    <a href="{{ route('teachers.edit', $teacher) }}" class="p-1.5 bg-slate-100 dark:bg-slate-700 rounded-lg transition-colors hover:bg-blue-100 dark:hover:bg-blue-900/30" title="Edit">
                                        <i data-lucide="edit-2" class="w-3.5 h-3.5 text-blue-500"></i>
                                    </a>
                                    <button type="button"
                                            data-delete-url="{{ route('teachers.destroy', $teacher) }}"
                                            data-delete-label="guru {{ addslashes($teacher->name) }}"
                                            class="delete-btn p-1.5 bg-slate-100 dark:bg-slate-700 rounded-lg transition-colors hover:bg-red-100 dark:hover:bg-red-900/30" title="Hapus">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5 text-red-500"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                                        <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada data guru</p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Mulai dengan menambahkan guru pertama</p>
                                    </div>
                                    <a href="{{ route('teachers.create') }}" class="btn-primary flex items-center gap-2 text-sm">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                        Tambah Guru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($teachers->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                <div class="relative inline-block">
                    <div id="photoPreview" class="w-28 h-28 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center border-2 border-dashed border-slate-300 dark:border-slate-600 cursor-pointer hover:border-gold-400 transition-colors" onclick="document.getElementById('photo').click()">
                        <img id="previewImg" src="{{ asset('images/default-teacher.png') }}" class="w-28 h-28 rounded-full object-cover shadow-lg">
                        <div class="absolute inset-0 flex flex-col items-center justify-center opacity-0 hover:opacity-100 bg-black/20 rounded-full transition-opacity">
                            <i data-lucide="camera" class="w-8 h-8 text-white"></i>
                            <span class="text-[10px] text-white font-bold uppercase tracking-wider">Ubah Foto</span>
                        </div>
                    </div>
                </div>
                {{ $teachers->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ====================================================== --}}
{{-- PREMIUM DELETE MODAL - Pure HTML/CSS/JS (No Alpine.js) --}}
{{-- ====================================================== --}}
<div id="deleteModal"
     style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:1rem;">
    
    {{-- Backdrop --}}
    <div id="deleteModalBackdrop"
         style="position:absolute; inset:0; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px);">
    </div>

    {{-- Modal Card --}}
    <div id="deleteModalBox"
         style="position:relative; z-index:1; background:white; border-radius:1.5rem; box-shadow:0 25px 60px rgba(0,0,0,0.25); border:1px solid #e2e8f0; width:100%; max-width:440px; transition:transform 0.25s ease, opacity 0.25s ease; transform:scale(0.9); opacity:0;">
        
        {{-- Body --}}
        <div style="padding:2.5rem 2rem 1.5rem; display:flex; flex-direction:column; align-items:center; text-align:center;">
            <div style="width:80px; height:80px; background:#FEF2F2; border-radius:1.25rem; display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                    <path d="M12 9v4"/><path d="M12 17h.01"/>
                </svg>
            </div>
            <h3 style="font-size:1.5rem; font-weight:800; color:#0F172A; margin:0 0 0.75rem;">Konfirmasi Hapus</h3>
            <p style="font-size:0.9rem; color:#64748B; line-height:1.6; margin:0;">
                Apakah Anda yakin ingin menghapus<br>
                <strong id="deleteTargetLabel" style="color:#DC2626;"></strong>?<br>
                Tindakan ini <strong>permanen</strong> dan tidak dapat dibatalkan.
            </p>
        </div>

        {{-- Actions --}}
        <div style="padding:1rem 2rem 2rem; display:flex; flex-direction:column; gap:0.75rem;">
            <form id="deleteForm" method="POST" action="" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" id="deleteSubmitBtn"
                        style="width:100%; padding:0.85rem 1.5rem; background:#DC2626; color:white; font-size:0.9rem; font-weight:700; border:none; border-radius:0.875rem; cursor:pointer; box-shadow:0 8px 20px rgba(220,38,38,0.3); transition:all 0.2s;">
                    Ya, Hapus Data
                </button>
            </form>
            <button type="button" id="deleteCancelBtn"
                    style="width:100%; padding:0.85rem 1.5rem; background:white; color:#374151; font-size:0.9rem; font-weight:700; border:2px solid #E5E7EB; border-radius:0.875rem; cursor:pointer; transition:all 0.2s;">
                Batalkan
            </button>
        </div>
    </div>
</div>

{{-- ====================================================== --}}
{{-- STATUS TOGGLE MODAL (Activate = Green / Deactivate = Orange) --}}
{{-- ====================================================== --}}
<div id="statusModal"
     style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:1rem;">

    {{-- Backdrop --}}
    <div id="statusModalBackdrop"
         style="position:absolute; inset:0; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px);">
    </div>

    {{-- Modal Card --}}
    <div id="statusModalBox"
         style="position:relative; z-index:1; background:white; border-radius:1.5rem; box-shadow:0 25px 60px rgba(0,0,0,0.25); border:1px solid #e2e8f0; width:100%; max-width:440px; transition:transform 0.25s ease, opacity 0.25s ease; transform:scale(0.9); opacity:0;">

        {{-- Body --}}
        <div style="padding:2.5rem 2rem 1.5rem; display:flex; flex-direction:column; align-items:center; text-align:center;">
            <div id="statusModalIcon"
                 style="width:80px; height:80px; border-radius:1.25rem; display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
            </div>
            <h3 id="statusModalTitle" style="font-size:1.5rem; font-weight:800; color:#0F172A; margin:0 0 0.75rem;"></h3>
            <p style="font-size:0.9rem; color:#64748B; line-height:1.6; margin:0;">
                <span id="statusModalBody"></span>
            </p>
        </div>

        {{-- Actions --}}
        <div style="padding:1rem 2rem 2rem; display:flex; flex-direction:column; gap:0.75rem;">
            <button type="button" id="statusConfirmBtn"
                    style="width:100%; padding:0.85rem 1.5rem; color:white; font-size:0.9rem; font-weight:700; border:none; border-radius:0.875rem; cursor:pointer; transition:all 0.2s;">
            </button>
            <button type="button" id="statusCancelBtn"
                    style="width:100%; padding:0.85rem 1.5rem; background:white; color:#374151; font-size:0.9rem; font-weight:700; border:2px solid #E5E7EB; border-radius:0.875rem; cursor:pointer; transition:all 0.2s;">
                Batalkan
            </button>
        </div>
    </div>
</div>

{{-- Dark mode styles for modals --}}
<style>
    /* Delete Modal */
    .dark #deleteModalBox { background: #1E293B !important; border-color: #334155 !important; }
    .dark #deleteModalBox h3 { color: #F8FAFC !important; }
    .dark #deleteModalBox p { color: #94A3B8 !important; }
    .dark #deleteCancelBtn { background: #1E293B !important; color: #CBD5E1 !important; border-color: #475569 !important; }
    .dark #deleteModalBox > div:first-child > div:first-child { background: rgba(220,38,38,0.15) !important; }
    #deleteSubmitBtn:hover { background: #B91C1C !important; transform: scale(1.02); }
    #deleteCancelBtn:hover { background: #F8FAFC !important; }
    /* Status Modal */
    .dark #statusModalBox { background: #1E293B !important; border-color: #334155 !important; }
    .dark #statusModalTitle { color: #F8FAFC !important; }
    .dark #statusModalBox p { color: #94A3B8 !important; }
    .dark #statusCancelBtn { background: #1E293B !important; color: #CBD5E1 !important; border-color: #475569 !important; }
    #statusCancelBtn:hover { background: #F8FAFC !important; }
</style>

<script>
    // ============================================
    // Modal State
    // ============================================
    let _isBulkDelete = false;
    let _statusToggleValue = null; // null | true (activate) | false (deactivate)

    // ============================================
    // Open / Close Modal
    // ============================================
    function showDeleteModal(url, label, isBulk) {
        _isBulkDelete = !!isBulk;
        document.getElementById('deleteTargetLabel').textContent = label;

        if (!isBulk) {
            document.getElementById('deleteForm').action = url;
        }

        const modal = document.getElementById('deleteModal');
        const box   = document.getElementById('deleteModalBox');

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => requestAnimationFrame(() => {
            box.style.transform = 'scale(1)';
            box.style.opacity   = '1';
        }));
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        const box   = document.getElementById('deleteModalBox');

        box.style.transform = 'scale(0.9)';
        box.style.opacity   = '0';

        setTimeout(() => {
            modal.style.display          = 'none';
            document.body.style.overflow = '';
            _isBulkDelete                = false;
        }, 250);
    }

    // ============================================
    // Wire up buttons (after DOM is ready)
    // ============================================
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();

        // Delete - Cancel button & Backdrop
        document.getElementById('deleteCancelBtn').addEventListener('click', closeDeleteModal);
        document.getElementById('deleteModalBackdrop').addEventListener('click', closeDeleteModal);

        // Delete submit
        document.getElementById('deleteForm').addEventListener('submit', function (e) {
            if (_isBulkDelete) {
                e.preventDefault();
                closeDeleteModal();
                submitBulkDeleteForm();
            }
        });

        // Per-row delete buttons
        document.querySelectorAll('.delete-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const url   = this.getAttribute('data-delete-url');
                const label = this.getAttribute('data-delete-label');
                showDeleteModal(url, label, false);
            });
        });

        // Status Modal - Cancel & Backdrop
        document.getElementById('statusCancelBtn').addEventListener('click', closeStatusModal);
        document.getElementById('statusModalBackdrop').addEventListener('click', closeStatusModal);

        // Status Modal - Confirm
        document.getElementById('statusConfirmBtn').addEventListener('click', function () {
            closeStatusModal();
            submitBulkToggleForm(_statusToggleValue);
        });

        // Auto-hide flash notifications
        const notification = document.querySelector('.slide-up.bg-green-50, .slide-up.bg-red-50');
        if (notification) {
            setTimeout(() => {
                notification.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                notification.style.opacity    = '0';
                notification.style.transform  = 'translateY(-10px)';
                setTimeout(() => notification.remove(), 500);
            }, 2000);
        }
    });

    // ============================================
    // Bulk Delete
    // ============================================
    function triggerBulkDelete() {
        const count = document.querySelectorAll('.teacher-checkbox:checked').length;
        if (count === 0) { alert('Pilih guru terlebih dahulu'); return; }
        showDeleteModal('', count + ' guru', true);
    }

    function submitBulkDeleteForm() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('teachers.bulk-delete') }}";

        const csrf = document.createElement('input');
        csrf.type  = 'hidden';
        csrf.name  = '_token';
        csrf.value = "{{ csrf_token() }}";
        form.appendChild(csrf);

        const method = document.createElement('input');
        method.type  = 'hidden';
        method.name  = '_method';
        method.value = 'DELETE';
        form.appendChild(method);

        document.querySelectorAll('.teacher-checkbox:checked').forEach(cb => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'teacher_ids[]';
            input.value = cb.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }

    // ============================================
    // Bulk Select
    // ============================================
    let selectedTeachers = [];

    function toggleSelectAll() {
        const all = document.getElementById('selectAll').checked;
        document.querySelectorAll('.teacher-checkbox').forEach(cb => cb.checked = all);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checked = document.querySelectorAll('.teacher-checkbox:checked');
        selectedTeachers = Array.from(checked).map(cb => cb.value);

        const bar   = document.getElementById('bulkActions');
        const count = document.getElementById('selectedCount');
        if (selectedTeachers.length > 0) {
            bar.classList.remove('hidden');
            count.textContent = selectedTeachers.length;
        } else {
            bar.classList.add('hidden');
        }
    }

    function clearSelection() {
        document.getElementById('selectAll').checked = false;
        document.querySelectorAll('.teacher-checkbox').forEach(cb => cb.checked = false);
        updateBulkActions();
    }

    // ============================================
    // Status Modal (Activate / Deactivate)
    // ============================================
    function showStatusModal(status) {
        const count = selectedTeachers.length;
        if (count === 0) { alert('Pilih guru terlebih dahulu'); return; }

        _statusToggleValue = status;

        const isActivate = status === true;

        // Colors
        const iconBg     = isActivate ? '#F0FDF4' : '#FFF7ED';
        const iconStroke = isActivate ? '#16A34A' : '#EA580C';
        const titleColor = isActivate ? '#15803D' : '#C2410C';
        const btnBg      = isActivate ? '#16A34A' : '#EA580C';
        const btnShadow  = isActivate ? 'rgba(22,163,74,0.3)' : 'rgba(234,88,12,0.3)';
        const btnHover   = isActivate ? '#15803D' : '#C2410C';

        // Icon SVG
        const iconSVG = isActivate
            ? `<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="${iconStroke}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>`
            : `<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="${iconStroke}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="10" x2="10" y1="15" y2="9"/><line x1="14" x2="14" y1="15" y2="9"/></svg>`;

        const title  = isActivate ? 'Konfirmasi Aktifkan' : 'Konfirmasi Nonaktifkan';
        const action = isActivate ? 'mengaktifkan' : 'menonaktifkan';
        const btnText = isActivate ? `Ya, Aktifkan ${count} Guru` : `Ya, Nonaktifkan ${count} Guru`;

        // Apply
        const iconEl = document.getElementById('statusModalIcon');
        iconEl.style.background = iconBg;
        iconEl.innerHTML = iconSVG;

        const titleEl = document.getElementById('statusModalTitle');
        titleEl.style.color = titleColor;
        titleEl.textContent = title;

        document.getElementById('statusModalBody').innerHTML =
            `Apakah Anda yakin ingin <strong>${action}</strong><br>` +
            `<strong style="color:${titleColor}">${count} guru</strong> yang dipilih?`;

        const confirmBtn = document.getElementById('statusConfirmBtn');
        confirmBtn.textContent = btnText;
        confirmBtn.style.background = btnBg;
        confirmBtn.style.boxShadow  = `0 8px 20px ${btnShadow}`;
        confirmBtn.onmouseover = () => confirmBtn.style.background = btnHover;
        confirmBtn.onmouseleave = () => confirmBtn.style.background = btnBg;

        // Show
        const modal = document.getElementById('statusModal');
        const box   = document.getElementById('statusModalBox');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => requestAnimationFrame(() => {
            box.style.transform = 'scale(1)';
            box.style.opacity   = '1';
        }));
    }

    function closeStatusModal() {
        const modal = document.getElementById('statusModal');
        const box   = document.getElementById('statusModalBox');
        box.style.transform = 'scale(0.9)';
        box.style.opacity   = '0';
        setTimeout(() => {
            modal.style.display          = 'none';
            document.body.style.overflow = '';
            _statusToggleValue           = null;
        }, 250);
    }

    function bulkToggleStatus(status) {
        if (selectedTeachers.length === 0) { alert('Pilih guru terlebih dahulu'); return; }
        showStatusModal(status);
    }

    function submitBulkToggleForm(status) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('teachers.bulk-toggle') }}";

        [
            { name: '_token',  value: "{{ csrf_token() }}" },
            { name: 'status',  value: status ? '1' : '0'  }
        ].forEach(({ name, value }) => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = name;
            inp.value = value;
            form.appendChild(inp);
        });

        selectedTeachers.forEach(id => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'teacher_ids[]';
            inp.value = id;
            form.appendChild(inp);
        });

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection