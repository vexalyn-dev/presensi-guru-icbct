@extends('layouts.app')

@section('page-title', 'Notifikasi')

@section('content')
@php
    $totalNotif  = $notifications->total();
    $unreadCount = auth()->user()->unreadCount();
    $readCount   = $totalNotif - $unreadCount;
@endphp

<div class="fade-in" x-data="notifPage()" @keydown.escape.window="selectionMode = false; selectedItems = []">

    {{-- ══════════════════════════════════════
         HEADER
    ══════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="relative w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/25 dark:shadow-gold-400/25 flex-shrink-0">
                <i data-lucide="bell" class="w-6 h-6 text-white dark:text-navy-900"></i>
                @if($unreadCount > 0)
                <span class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-white dark:border-slate-900">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white tracking-tight">Notifikasi</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5" x-show="!selectionMode">
                    @if($unreadCount > 0)
                        <span class="text-red-500 font-semibold">{{ $unreadCount }}</span> belum dibaca dari {{ $totalNotif }} total
                    @else
                        Semua {{ $totalNotif }} notifikasi sudah dibaca
                    @endif
                </p>
                <p class="text-sm text-navy-800 dark:text-gold-400 font-semibold mt-0.5" x-show="selectionMode" x-cloak>
                    <span x-text="selectedItems.length"></span> item dipilih
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            {{-- Bulk action buttons (selection mode) --}}
            <div x-show="selectionMode" x-cloak class="flex items-center gap-2">
                <button @click="bulkMarkRead()"
                        x-show="selectedItems.length > 0"
                        class="flex items-center gap-1.5 px-3.5 py-2 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl text-sm font-semibold hover:opacity-90 transition-all">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Tandai Dibaca</span>
                </button>
                <button @click="bulkDelete()"
                        x-show="selectedItems.length > 0"
                        class="flex items-center gap-1.5 px-3.5 py-2 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 transition-all">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Hapus (<span x-text="selectedItems.length"></span>)</span>
                </button>
                <button @click="cancelSelection()"
                        class="flex items-center gap-1.5 px-3.5 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-300 dark:hover:bg-slate-600 transition-all">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Batal</span>
                </button>
            </div>

            {{-- Normal action buttons --}}
            <div x-show="!selectionMode" class="flex items-center gap-2">
                @if($unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-2 px-4 py-2.5 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl text-sm font-semibold hover:opacity-90 transition-all hover:-translate-y-0.5 shadow-lg shadow-navy-800/20 dark:shadow-gold-400/20">
                        <i data-lucide="check-check" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Tandai Semua Dibaca</span>
                        <span class="sm:hidden">Baca Semua</span>
                    </button>
                </form>
                @endif

                @if($totalNotif > 0)
                <button @click="openClearModal()"
                        class="flex items-center gap-2 px-4 py-2.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-xl text-sm font-semibold hover:bg-red-100 dark:hover:bg-red-900/30 transition-all">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Hapus Semua</span>
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         STATS BAR
    ══════════════════════════════════════ --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="card p-4 flex items-center gap-3 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="bell" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total</p>
                <p class="text-xl font-bold text-navy-800 dark:text-white leading-tight">{{ $totalNotif }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="bell-ring" class="w-5 h-5 text-red-500 dark:text-red-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Belum Dibaca</p>
                <p class="text-xl font-bold text-navy-800 dark:text-white leading-tight">{{ $unreadCount }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Sudah Dibaca</p>
                <p class="text-xl font-bold text-navy-800 dark:text-white leading-tight">{{ $readCount }}</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         FILTER + SELECT ALL BAR
    ══════════════════════════════════════ --}}
    <div class="card p-3 mb-4 flex items-center justify-between gap-3">
        {{-- Filter tabs --}}
        <div class="flex items-center gap-1 overflow-x-auto scrollbar-hide">
            <button @click="activeFilter = 'all'"
                    :class="activeFilter === 'all' ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow-md' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
                <i data-lucide="layout-list" class="w-3.5 h-3.5"></i>
                Semua
            </button>
            <button @click="activeFilter = 'unread'"
                    :class="activeFilter === 'unread' ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow-md' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
                <i data-lucide="circle-dot" class="w-3.5 h-3.5"></i>
                Belum Dibaca
                @if($unreadCount > 0)
                <span class="ml-0.5 px-1.5 py-0.5 bg-red-500 text-white rounded-full text-[9px] font-black">{{ $unreadCount }}</span>
                @endif
            </button>
            <button @click="activeFilter = 'read'"
                    :class="activeFilter === 'read' ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow-md' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                Sudah Dibaca
            </button>
        </div>

        {{-- Select all + select mode toggle --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            <button x-show="selectionMode && visibleItems().length > 0" x-cloak
                    @click="toggleSelectAll()"
                    class="text-xs font-semibold text-navy-800 dark:text-gold-400 hover:underline whitespace-nowrap">
                <span x-text="selectedItems.length === visibleItems().length ? 'Batal Pilih Semua' : 'Pilih Semua'"></span>
            </button>
            <button @click="selectionMode ? cancelSelection() : (selectionMode = true)"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap"
                    :class="selectionMode ? 'bg-navy-100 dark:bg-navy-900/40 text-navy-800 dark:text-gold-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'">
                <i data-lucide="check-square" class="w-3.5 h-3.5"></i>
                <span x-text="selectionMode ? 'Pilih Mode' : 'Pilih'"></span>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         NOTIFICATIONS LIST
    ══════════════════════════════════════ --}}
    <div class="space-y-2">
        @forelse($notifications as $notif)
        <div class="group relative"
             x-show="shouldShow('{{ $notif->id }}', {{ $notif->is_read ? 'true' : 'false' }})"
             x-data="{ swipeX: 0, swiping: false, startX: 0 }"
             @touchstart.passive="startX = $event.touches[0].clientX; swiping = false"
             @touchmove.passive="
                const dx = $event.touches[0].clientX - startX;
                if (Math.abs(dx) > 8) { swiping = true; swipeX = Math.min(0, Math.max(-90, dx)); }
             "
             @touchend="if (swiping && swipeX < -60) { swipeX = -80; } else { swipeX = 0; swiping = false; }"
             @contextmenu.prevent="addToSelection('{{ $notif->id }}'); selectionMode = true">

            {{-- Swipe delete background --}}
            <div class="absolute inset-y-0 right-0 w-20 bg-red-500 rounded-2xl flex items-center justify-center"
                 :class="swipeX < -50 ? 'opacity-100' : 'opacity-0'"
                 style="transition: opacity 0.15s ease;">
                <button @click="confirmDelete('{{ $notif->id }}', '{{ addslashes($notif->title) }}')" class="text-white p-2">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                </button>
            </div>

            {{-- Main card --}}
            <div class="card overflow-hidden transition-all duration-200 hover:shadow-md relative"
                 :style="`transform: translateX(${swipeX}px); transition: ${swiping ? 'none' : 'transform 0.25s ease'}`"
                 :class="{
                     'ring-2 ring-navy-800 dark:ring-gold-400 shadow-md': selectedItems.includes('{{ $notif->id }}'),
                     '{{ !$notif->is_read ? 'border-l-[3px] border-l-blue-500' : '' }}'
                 }">

                {{-- Unread indicator glow --}}
                @if(!$notif->is_read)
                <div class="absolute inset-0 bg-blue-500/[0.03] dark:bg-blue-400/[0.05] pointer-events-none"></div>
                @endif

                <div class="p-4 sm:p-5 flex items-start gap-3 sm:gap-4">

                    {{-- Checkbox --}}
                    <div x-show="selectionMode" x-cloak class="flex-shrink-0 pt-0.5">
                        <div @click="toggleSelection('{{ $notif->id }}')"
                             class="w-5 h-5 rounded-md border-2 flex items-center justify-center cursor-pointer transition-all"
                             :class="selectedItems.includes('{{ $notif->id }}')
                                 ? 'bg-navy-800 dark:bg-gold-400 border-navy-800 dark:border-gold-400'
                                 : 'border-slate-300 dark:border-slate-600 hover:border-navy-400 dark:hover:border-gold-500'">
                            <svg x-show="selectedItems.includes('{{ $notif->id }}')" class="w-3 h-3 text-white dark:text-navy-900" fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2 6l3 3 5-5"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Icon --}}
                    <div x-show="!selectionMode" class="relative flex-shrink-0">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl {{ $notif->color }} flex items-center justify-center shadow-sm">
                            <i data-lucide="{{ $notif->icon }}" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        </div>
                        @if(!$notif->is_read)
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 rounded-full border-2 border-white dark:border-slate-800 shadow"></span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h3 class="text-sm font-bold text-navy-800 dark:text-white leading-snug {{ !$notif->is_read ? '' : 'opacity-80' }}">
                                {{ $notif->title }}
                            </h3>
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                @if(!$notif->is_read)
                                <span class="inline-flex items-center px-1.5 py-0.5 bg-blue-500 text-white rounded-full text-[9px] font-black tracking-wide">BARU</span>
                                @endif
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 whitespace-nowrap">
                                    {{ $notif->created_at->locale('id')->diffForHumans() }}
                                </span>
                                {{-- Desktop delete button --}}
                                <button x-show="!selectionMode" x-cloak
                                        @click.stop="confirmDelete('{{ $notif->id }}', '{{ addslashes($notif->title) }}')"
                                        class="hidden sm:flex opacity-0 group-hover:opacity-100 w-7 h-7 items-center justify-center rounded-lg bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-500 dark:text-red-400 transition-all">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>

                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed line-clamp-2 {{ $notif->is_read ? 'opacity-70' : '' }}">
                            {{ $notif->message }}
                        </p>

                        <div class="flex items-center justify-between mt-2.5 gap-2">
                            <div class="flex items-center gap-1 text-[10px] text-slate-400 dark:text-slate-500">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                {{ $notif->created_at->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                            </div>

                            <div class="flex items-center gap-2">
                                @if($notif->action_url)
                                <a href="{{ $notif->action_url }}"
                                   @click="markAsRead('{{ $notif->id }}')"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-navy-800 dark:text-gold-400 hover:underline">
                                    Lihat Detail
                                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                </a>
                                @endif
                                @if(!$notif->is_read)
                                <button @click.stop="markAsRead('{{ $notif->id }}')"
                                        class="text-[10px] text-slate-400 dark:text-slate-500 hover:text-navy-800 dark:hover:text-gold-400 font-medium transition-colors">
                                    Tandai dibaca
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card p-14 sm:p-20 text-center">
            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-5 shadow-inner">
                <i data-lucide="bell-off" class="w-10 h-10 sm:w-12 sm:h-12 text-slate-400 dark:text-slate-500"></i>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-navy-800 dark:text-white mb-2">Tidak Ada Notifikasi</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-xs mx-auto">Semua pemberitahuan akan muncul di sini</p>
        </div>
        @endforelse
    </div>

    {{-- ══════════════════════════════════════
         PAGINATION
    ══════════════════════════════════════ --}}
    @if($notifications->hasPages())
    <div class="card p-4 mt-4">
        {{ $notifications->links() }}
    </div>
    @endif

</div>

{{-- ══════════════════════════════════════
     DELETE CONFIRM MODAL
══════════════════════════════════════ --}}
<div id="notif-delete-modal"
     style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:1rem;">
    <div id="notif-delete-backdrop"
         style="position:absolute; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(6px);"
         onclick="closeDeleteModal()"></div>
    <div id="notif-delete-box"
         style="position:relative; z-index:1; background:white; border-radius:1.25rem; box-shadow:0 32px 64px rgba(0,0,0,0.22); border:1px solid #e2e8f0; width:100%; max-width:360px; transform:scale(0.92) translateY(8px); opacity:0; transition:all 0.25s cubic-bezier(0.34,1.56,0.64,1);">
        <div style="padding:2rem 1.75rem 1.25rem; text-align:center;">
            <div style="width:56px; height:56px; background:#FEF2F2; border-radius:14px; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/><path d="m19 6-.867 12.142A2 2 0 0 1 16.138 20H7.862a2 2 0 0 1-1.995-1.858L5 6m5 0V4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2"/>
                </svg>
            </div>
            <h3 style="font-size:1.125rem; font-weight:800; color:#0F172A; margin:0 0 0.5rem;">Hapus Notifikasi?</h3>
            <p id="notif-delete-msg" style="font-size:0.8125rem; color:#64748B; line-height:1.6; margin:0;"></p>
        </div>
        <div style="padding:0.75rem 1.75rem 1.75rem; display:flex; flex-direction:column; gap:0.5rem;">
            <button id="notif-delete-confirm"
                    style="padding:0.8125rem; background:#DC2626; color:white; font-size:0.875rem; font-weight:700; border:none; border-radius:0.875rem; cursor:pointer; transition:all 0.2s; box-shadow:0 4px 14px rgba(220,38,38,0.3);">
                Ya, Hapus
            </button>
            <button onclick="closeDeleteModal()"
                    style="padding:0.8125rem; background:#F1F5F9; color:#374151; font-size:0.875rem; font-weight:700; border:none; border-radius:0.875rem; cursor:pointer; transition:all 0.2s;">
                Batal
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     CLEAR ALL MODAL
══════════════════════════════════════ --}}
<div id="notif-clear-modal"
     style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:1rem;">
    <div style="position:absolute; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(6px);"
         onclick="closeClearModal()"></div>
    <div id="notif-clear-box"
         style="position:relative; z-index:1; background:white; border-radius:1.25rem; box-shadow:0 32px 64px rgba(0,0,0,0.22); border:1px solid #e2e8f0; width:100%; max-width:360px; transform:scale(0.92) translateY(8px); opacity:0; transition:all 0.25s cubic-bezier(0.34,1.56,0.64,1);">
        <div style="padding:2rem 1.75rem 1.25rem; text-align:center;">
            <div style="width:56px; height:56px; background:#FFF7ED; border-radius:14px; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#F97316" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/>
                </svg>
            </div>
            <h3 style="font-size:1.125rem; font-weight:800; color:#0F172A; margin:0 0 0.5rem;">Hapus Semua Notifikasi?</h3>
            <p style="font-size:0.8125rem; color:#64748B; line-height:1.6; margin:0;">Semua <strong>{{ $totalNotif }} notifikasi</strong> akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div style="padding:0.75rem 1.75rem 1.75rem; display:flex; flex-direction:column; gap:0.5rem;">
            <form action="{{ route('notifications.clear') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                        style="width:100%; padding:0.8125rem; background:#DC2626; color:white; font-size:0.875rem; font-weight:700; border:none; border-radius:0.875rem; cursor:pointer; transition:all 0.2s; box-shadow:0 4px 14px rgba(220,38,38,0.3);">
                    Ya, Hapus Semua
                </button>
            </form>
            <button onclick="closeClearModal()"
                    style="padding:0.8125rem; background:#F1F5F9; color:#374151; font-size:0.875rem; font-weight:700; border:none; border-radius:0.875rem; cursor:pointer; transition:all 0.2s;">
                Batal
            </button>
        </div>
    </div>
</div>

<style>
    .dark #notif-delete-box,
    .dark #notif-clear-box {
        background: #1E293B !important;
        border-color: #334155 !important;
    }
    .dark #notif-delete-box h3,
    .dark #notif-clear-box h3 { color: #F8FAFC !important; }
    .dark #notif-delete-box p,
    .dark #notif-clear-box p { color: #94A3B8 !important; }
    .dark #notif-delete-box button:last-child,
    .dark #notif-clear-box button:last-child {
        background: #0F172A !important;
        color: #CBD5E1 !important;
    }
    .dark #notif-delete-box > div:first-child > div:first-child { background: rgba(220,38,38,0.15) !important; }
    .dark #notif-clear-box > div:first-child > div:first-child { background: rgba(249,115,22,0.15) !important; }
    [x-cloak] { display: none !important; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<script>
function notifPage() {
    return {
        activeFilter: 'all',
        selectionMode: false,
        selectedItems: [],

        // Map of all notif IDs + read status
        notifData: {
            @foreach($notifications as $n)
            '{{ $n->id }}': {{ $n->is_read ? 'true' : 'false' }},
            @endforeach
        },

        shouldShow(id, isRead) {
            if (this.activeFilter === 'all') return true;
            if (this.activeFilter === 'unread') return !isRead;
            if (this.activeFilter === 'read') return isRead;
            return true;
        },

        visibleItems() {
            return Object.entries(this.notifData)
                .filter(([id, isRead]) => this.shouldShow(id, isRead))
                .map(([id]) => id);
        },

        toggleSelection(id) {
            const idx = this.selectedItems.indexOf(id);
            if (idx > -1) this.selectedItems.splice(idx, 1);
            else this.selectedItems.push(id);
        },

        addToSelection(id) {
            if (!this.selectedItems.includes(id)) this.selectedItems.push(id);
        },

        cancelSelection() {
            this.selectionMode = false;
            this.selectedItems = [];
        },

        toggleSelectAll() {
            const visible = this.visibleItems();
            if (this.selectedItems.length === visible.length) {
                this.selectedItems = [];
            } else {
                this.selectedItems = [...visible];
            }
        },

        markAsRead(id) {
            if (this.notifData[id]) return; // already read
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
            }).then(r => r.ok && (this.notifData[id] = true));
        },

        confirmDelete(id, title) {
            openDeleteModal(id, `Notifikasi "${title}" akan dihapus permanen.`);
        },

        bulkDelete() {
            if (!this.selectedItems.length) return;
            const ids = [...this.selectedItems];
            const count = ids.length;
            openDeleteModal(null, `${count} notifikasi yang dipilih akan dihapus permanen.`, () => {
                fetch('{{ route("notifications.clear") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-HTTP-Method-Override': 'DELETE',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ ids }),
                }).then(() => location.reload());
            });
        },

        bulkMarkRead() {
            const ids = [...this.selectedItems];
            Promise.all(ids.map(id =>
                fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                })
            )).then(() => location.reload());
        },

        openClearModal() {
            openClearModal();
        },
    };
}

// ── Delete Modal helpers ──────────────────────────────
let _deleteCallback = null;

function openDeleteModal(id, msg, customCallback) {
    const modal = document.getElementById('notif-delete-modal');
    const box   = document.getElementById('notif-delete-box');
    document.getElementById('notif-delete-msg').textContent = msg;
    modal.style.display = 'flex';
    requestAnimationFrame(() => {
        box.style.transform = 'scale(1) translateY(0)';
        box.style.opacity   = '1';
    });
    _deleteCallback = customCallback || function() {
        fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        }).then(() => location.reload());
    };
    document.getElementById('notif-delete-confirm').onclick = () => {
        closeDeleteModal();
        _deleteCallback && _deleteCallback();
    };
}

function closeDeleteModal() {
    const modal = document.getElementById('notif-delete-modal');
    const box   = document.getElementById('notif-delete-box');
    box.style.transform = 'scale(0.92) translateY(8px)';
    box.style.opacity   = '0';
    setTimeout(() => { modal.style.display = 'none'; }, 250);
}

// ── Clear All Modal helpers ───────────────────────────
function openClearModal() {
    const modal = document.getElementById('notif-clear-modal');
    const box   = document.getElementById('notif-clear-box');
    modal.style.display = 'flex';
    requestAnimationFrame(() => {
        box.style.transform = 'scale(1) translateY(0)';
        box.style.opacity   = '1';
    });
}

function closeClearModal() {
    const modal = document.getElementById('notif-clear-modal');
    const box   = document.getElementById('notif-clear-box');
    box.style.transform = 'scale(0.92) translateY(8px)';
    box.style.opacity   = '0';
    setTimeout(() => { modal.style.display = 'none'; }, 250);
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeDeleteModal(); closeClearModal(); }
});
</script>

@endsection
