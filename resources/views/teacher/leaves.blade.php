@extends('layouts.app')

@section('page-title', 'Riwayat Izin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 fade-in">
    
    <!-- Tabel Riwayat Izin -->
    <div class="card overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
            <h3 class="text-base font-semibold text-navy-800 dark:text-white">Daftar Riwayat Izin & Sakit</h3>
            <a href="{{ route('teacher.leaves.create') }}" class="px-4 py-2 bg-navy-800 text-white text-xs font-semibold rounded-lg hover:bg-navy-900 transition-all flex items-center gap-2 icon-click">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                Ajukan Izin Baru
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 uppercase">Jenis</th>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 uppercase">Alasan</th>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($leaves as $leave)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-navy-800 dark:text-white">
                                    {{ $leave->start_date->format('d M Y') }}
                                    @if($leave->start_date != $leave->end_date)
                                        - {{ $leave->end_date->format('d M Y') }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-600 dark:text-slate-300">{{ $leave->type }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-600 dark:text-slate-300 line-clamp-2 max-w-xs" title="{{ $leave->reason }}">{{ $leave->reason }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'Pending' => 'bg-yellow-100 text-yellow-700',
                                        'Approved' => 'bg-green-100 text-green-700',
                                        'Rejected' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $statusColors[$leave->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $leave->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">Belum ada riwayat pengajuan izin/sakit.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200 dark:border-slate-700">
            {{ $leaves->links() }}
        </div>
    </div>

</div>
@endsection
