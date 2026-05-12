@extends('layouts.app')

@section('page-title', 'QR Code Guru')

@section('content')
<div class="fade-in">
    
    <!-- Custom Confirm Modal -->
    <div id="confirm-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="confirm-content">
            <!-- Header -->
            <div class="p-5 pb-3 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-400/30">
                        <i data-lucide="shield-alert" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Regenerate QR Code?</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
            </div>
            
            <!-- Body -->
            <div class="p-5">
                <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl mb-3">
                    <p class="text-sm text-amber-800 dark:text-amber-300 font-medium">
                        ⚠️ QR Code lama tidak akan bisa digunakan lagi. Lanjutkan?
                    </p>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    QR Code yang baru akan otomatis digenerate dan QR Code lama akan dihapus dari sistem.
                </p>
            </div>
            
            <!-- Footer -->
            <div class="p-5 pt-3 border-t border-slate-200 dark:border-slate-700 flex items-center gap-3">
                <button onclick="hideConfirmModal()" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-colors">
                    Batal
                </button>
                <button onclick="submitRegenerate()" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-amber-400/30">
                    Ya, Regenerate
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto">
        
        <!-- Header with Actions -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('teachers.show', $teacher) }}"
                    class="group flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600">
                    <i data-lucide="arrow-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
                    <span>Kembali</span>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-navy-800 dark:text-white">QR Code Presensi</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $teacher->name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('teachers.qr.download', $teacher) }}" class="flex items-center gap-2 px-4 py-2.5 bg-navy-800 hover:bg-navy-900 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-navy-800/30 icon-click">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Download
                </a>
                <button onclick="showConfirmModal()" class="flex items-center gap-2 px-4 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-colors icon-click">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Regenerate
                </button>
                <a href="{{ route('teachers.index') }}" class="flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-colors icon-click">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            
            <!-- Left: QR Code Display (2/3 width) - VERTICAL LAYOUT -->
            <div class="lg:col-span-2">
                <div class="card p-6 h-full">
                    <div class="flex flex-col items-center justify-center h-full">
                        <!-- Teacher Profile -->
                        <div class="text-center mb-6">
                            @if($teacher->photo)
                                <img src="{{ asset('storage/' . $teacher->photo) }}" 
                                     class="w-20 h-20 rounded-full object-cover border-3 border-slate-200 dark:border-slate-700 shadow-lg mx-auto">
                            @else
                                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-gold-400 to-gold-500 flex items-center justify-center text-navy-900 font-bold text-3xl shadow-lg shadow-gold-400/30 mx-auto">
                                    {{ substr($teacher->name, 0, 1) }}
                                </div>
                            @endif
                            <h2 class="text-lg font-bold text-navy-800 dark:text-white mt-3">{{ $teacher->name }}</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $teacher->email }}</p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-1">ID: {{ $teacher->formatted_id }}</p>
                        </div>

                        <!-- QR Code -->
                        <div class="relative p-4 bg-white rounded-xl shadow-lg mb-6">
                            @if($teacher->qr_code && file_exists(storage_path('app/public/' . $teacher->qr_code)))
                                <div class="w-56 h-56">
                                    <img src="{{ asset('storage/' . $teacher->qr_code) }}" alt="QR Code" class="w-full h-full object-contain">
                                </div>
                            @else
                                <div class="w-56 h-56 bg-slate-100 dark:bg-slate-700 rounded-lg flex flex-col items-center justify-center">
                                    <i data-lucide="qr-code" class="w-12 h-12 text-slate-400 mb-3"></i>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">QR Code belum digenerate</p>
                                    <button onclick="showConfirmModal()" class="px-5 py-2 bg-gold-400 hover:bg-gold-500 text-navy-900 rounded-lg text-xs font-semibold transition-colors">
                                        Generate
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Spacer to align with Security card -->
                        <div class="flex-grow"></div>
                    </div>
                </div>
            </div>

            <!-- Right: Info & Actions (1/3 width) -->
            <div class="space-y-4">
                
                <!-- Quick Actions - NO ICONS -->
                <div class="card p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-navy-800 dark:text-white">Aksi Cepat</h3>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('attendance.scan') }}" class="group flex flex-col items-center gap-2 p-4 bg-gradient-to-br from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 text-navy-900 rounded-xl transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                            <span class="text-sm font-bold">Scan</span>
                            <span class="text-[10px] text-navy-700">Sekarang</span>
                        </a>
                        <a href="{{ route('attendance.history') }}" class="group flex flex-col items-center gap-2 p-4 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                            <span class="text-sm font-bold">Riwayat</span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400">Presensi</span>
                        </a>
                    </div>
                </div>

                <!-- How to Use -->
                <div class="card p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-gold-400 to-gold-500 rounded-lg flex items-center justify-center">
                            <i data-lucide="info" class="w-4 h-4 text-navy-900"></i>
                        </div>
                        <h3 class="text-sm font-bold text-navy-800 dark:text-white">Cara Scan</h3>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex items-start gap-2">
                            <div class="w-5 h-5 bg-gold-100 dark:bg-gold-900/30 text-gold-600 dark:text-gold-400 rounded-full flex items-center justify-center text-[9px] font-bold flex-shrink-0">1</div>
                            <p class="text-xs text-slate-600 dark:text-slate-300">Buka halaman Scan QR Code</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <div class="w-5 h-5 bg-gold-100 dark:bg-gold-900/30 text-gold-600 dark:text-gold-400 rounded-full flex items-center justify-center text-[9px] font-bold flex-shrink-0">2</div>
                            <p class="text-xs text-slate-600 dark:text-slate-300">Klik tombol Mulai Scan</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <div class="w-5 h-5 bg-gold-100 dark:bg-gold-900/30 text-gold-600 dark:text-gold-400 rounded-full flex items-center justify-center text-[9px] font-bold flex-shrink-0">3</div>
                            <p class="text-xs text-slate-600 dark:text-slate-300">Arahkan ke QR Code ini</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <div class="w-5 h-5 bg-gold-100 dark:bg-gold-900/30 text-gold-600 dark:text-gold-400 rounded-full flex items-center justify-center text-[9px] font-bold flex-shrink-0">4</div>
                            <p class="text-xs text-slate-600 dark:text-slate-300">Tunggu hingga terdeteksi</p>
                        </div>
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="card p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                    <div class="flex items-start gap-2">
                        <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i data-lucide="shield-alert" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-amber-800 dark:text-amber-300 mb-1">Keamanan</h4>
                            <p class="text-[10px] text-amber-600 dark:text-amber-400 leading-relaxed">
                                Jangan bagikan QR Code ini kepada orang lain. QR Code ini adalah identitas presensi Anda dan hanya boleh digunakan oleh Anda sendiri. Jika QR Code hilang atau disalahgunakan, segera hubungi admin untuk regenerate.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Regenerate Form -->
<form id="regenerate-form" action="{{ route('teachers.qr.regenerate', $teacher) }}" method="POST" class="hidden">
    @csrf
</form>

<!-- Styles -->
<style>
    .pulse-dot {
        display: inline-block !important;
        animation: pulse-green 2s cubic-bezier(0.4, 0, 0.6, 1) infinite !important;
    }
    
    @keyframes pulse-green {
        0% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        50% { transform: scale(1.15); opacity: 0.85; box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    /* QR Code Styling */
    .card svg {
        width: 100%;
        height: 100%;
    }

    /* Modal Animation */
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    @keyframes modalOut {
        from { opacity: 1; transform: scale(1); }
        to { opacity: 0; transform: scale(0.95); }
    }

    .modal-enter {
        animation: modalIn 0.3s ease-out forwards;
    }

    .modal-exit {
        animation: modalOut 0.2s ease-in forwards;
    }
</style>

<script>
    // Modal Functions
    function showConfirmModal() {
        const modal = document.getElementById('confirm-modal');
        const content = document.getElementById('confirm-content');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('modal-enter');
        
        lucide.createIcons();
    }

    function hideConfirmModal() {
        const modal = document.getElementById('confirm-modal');
        const content = document.getElementById('confirm-content');
        
        content.classList.remove('modal-enter');
        content.classList.add('modal-exit');
        
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            content.classList.remove('modal-exit', 'scale-95', 'opacity-0');
        }, 200);
    }

    function submitRegenerate() {
        document.getElementById('regenerate-form').submit();
    }

    // Close modal on outside click
    document.getElementById('confirm-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideConfirmModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideConfirmModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endsection