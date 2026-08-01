@extends('layouts.piket')
@section('page-title', 'Dashboard Guru Piket')
@section('content')
<div class="space-y-6 fade-in">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg">
            <i data-lucide="shield" class="w-6 h-6 text-white dark:text-navy-900"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Dashboard Guru Piket</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Selamat datang, {{ auth()->user()->name }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('piket.attendance') }}" class="card p-5 flex items-center gap-4 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
            <div class="w-11 h-11 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                <i data-lucide="scan-line" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-navy-800 dark:text-white">Presensi Harian</p>
                <p class="text-xs text-slate-500">Scan masuk/keluar guru</p>
            </div>
        </a>
        <a href="{{ route('piket.class-attendance.manual') }}" class="card p-5 flex items-center gap-4 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
            <div class="w-11 h-11 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                <i data-lucide="clipboard-edit" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-navy-800 dark:text-white">Manual Presensi</p>
                <p class="text-xs text-slate-500">Input presensi manual</p>
            </div>
        </a>
        <a href="{{ route('piket.leave-approval') }}" class="card p-5 flex items-center gap-4 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
            <div class="w-11 h-11 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                <i data-lucide="check-circle" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-navy-800 dark:text-white">Approval Izin</p>
                <p class="text-xs text-slate-500">Review & setujui izin</p>
            </div>
        </a>
    </div>
</div>
<script>document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });</script>
<style>.fade-in{animation:fadeIn .4s ease-out}@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}</style>
@endsection
