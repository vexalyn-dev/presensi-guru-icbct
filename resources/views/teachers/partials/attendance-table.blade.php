<tbody class="divide-y divide-slate-200 dark:divide-slate-700">
    @forelse($attendances as $att)
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
            <td class="px-5 py-3">
                <p class="text-sm font-medium text-navy-800 dark:text-white">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($att->date)->locale('id')->isoFormat('dddd') }}</p>
            </td>
            <td class="px-5 py-3">
                @if($att->check_in)
                    <span class="text-sm font-mono text-slate-700 dark:text-slate-300">{{ $att->check_in }}</span>
                @else
                    <span class="text-xs text-slate-400">-</span>
                @endif
            </td>
            <td class="px-5 py-3">
                @if($att->check_out)
                    <span class="text-sm font-mono text-slate-700 dark:text-slate-300">{{ $att->check_out }}</span>
                @else
                    <span class="text-xs text-slate-400">Belum keluar</span>
                @endif
            </td>
            <td class="px-5 py-3">
                @php
                    $statusColors = [
                        'Hadir' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                        'Terlambat' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                        'Izin' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                        'Alpha' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                    ];
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$att->status] ?? 'bg-slate-100 text-slate-700' }}">
                    {{ $att->status }}
                </span>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="px-5 py-8 text-center">
                <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada riwayat absensi</p>
            </td>
        </tr>
    @endforelse
</tbody>

<!-- Pagination -->
@if($attendances->hasPages())
    <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400" id="attendance-page-info">
                Halaman {{ $attendances->currentPage() }} dari {{ $attendances->lastPage() }}
            </span>
            <div class="flex items-center gap-2">
                @if($attendances->onFirstPage())
                    <button disabled class="px-4 py-2 text-xs font-medium text-slate-400 bg-slate-200 dark:bg-slate-700 rounded-lg cursor-not-allowed">
                        ← Prev
                    </button>
                @else
                    <button type="button" data-page="{{ $attendances->previousPageUrl() }}" class="pagination-btn px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 rounded-lg transition-colors">
                        ← Prev
                    </button>
                @endif
                
                @if($attendances->hasMorePages())
                    <button type="button" data-page="{{ $attendances->nextPageUrl() }}" class="pagination-btn px-4 py-2 text-xs font-medium text-white bg-navy-800 hover:bg-navy-900 rounded-lg transition-colors">
                        Next →
                    </button>
                @else
                    <button disabled class="px-4 py-2 text-xs font-medium text-slate-400 bg-slate-200 dark:bg-slate-700 rounded-lg cursor-not-allowed">
                        Next →
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif
