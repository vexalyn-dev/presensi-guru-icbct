@extends('layouts.app')

@section('page-title', 'Jadwal Mengajar')

@section('content')
<div class="max-w-4xl mx-auto fade-in">
    <div class="card p-6">
        <div class="text-center py-10">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="calendar" class="w-8 h-8 text-blue-500"></i>
            </div>
            <h2 class="text-xl font-bold text-navy-800 dark:text-white mb-2">Jadwal Mengajar</h2>
            <p class="text-slate-500 dark:text-slate-400">Jadwal mengajar belum tersedia untuk saat ini.</p>
        </div>
    </div>
</div>
@endsection
