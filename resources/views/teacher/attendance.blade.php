@extends('layouts.app')

@section('page-title', 'Presensi Saya')

@section('content')
<div class="max-w-4xl mx-auto fade-in">
    <div class="card overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <h3 class="text-base font-semibold text-navy-800 dark:text-white">Riwayat Presensi Saya</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Daftar kehadiran terbaru Anda</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 uppercase">Jam Masuk</th>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 uppercase">Jam Keluar</th>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-navy-800 dark:text-white">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</p>
                                <p class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($att->date)->locale('id')->isoFormat('dddd') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($att->check_in)
                                    <span class="text-sm font-mono text-slate-700 dark:text-slate-300">{{ $att->check_in }}</span>
                                @else
                                    <span class="text-sm text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($att->check_out)
                                    <span class="text-sm font-mono text-slate-700 dark:text-slate-300">{{ $att->check_out }}</span>
                                @else
                                    <span class="text-sm text-slate-400">Belum keluar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'Hadir' => 'bg-green-100 text-green-700',
                                        'Terlambat' => 'bg-yellow-100 text-yellow-700',
                                        'Izin' => 'bg-blue-100 text-blue-700',
                                        'Alpha' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $statusColors[$att->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $att->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">Belum ada riwayat presensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200 dark:border-slate-700">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@endsection
