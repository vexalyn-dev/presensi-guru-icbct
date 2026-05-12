{{-- resources/views/teachers/partials/attendance-body.blade.php --}}
@php
    // Fallback jika $attendances tidak dikirim dari parent view
    $attendances = $attendances ?? [];
@endphp

@forelse($attendances as $attendance)
    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
        <td class="px-5 py-4">
            <span class="text-sm text-slate-600 dark:text-slate-300">
                {{ $attendance->created_at->format('d M Y') }}
            </span>
            <p class="text-[10px] text-slate-400">
                {{ $attendance->created_at->format('H:i') }}
            </p>
        </td>
        <td class="px-5 py-4">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                {{ $attendance->status === 'Hadir' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                {{ $attendance->status === 'Terlambat' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                {{ $attendance->status === 'Izin' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                {{ $attendance->status === 'Alpha' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                {{ !in_array($attendance->status, ['Hadir', 'Terlambat', 'Izin', 'Alpha']) ? 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400' : '' }}">
                {{ $attendance->status ?? '-' }}
            </span>
        </td>
        <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
            {{ $attendance->location ?? '-' }}
        </td>
        <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
            {{ $attendance->device_info ?? '-' }}
        </td>
        <td class="px-5 py-4">
            @if($attendance->note)
                <span class="text-xs text-slate-500 dark:text-slate-400" title="{{ $attendance->note }}">
                    {{ Str::limit($attendance->note, 30) }}
                </span>
            @else
                <span class="text-xs text-slate-400">-</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-5 py-8 text-center">
            <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                    <i data-lucide="inbox" class="w-6 h-6 text-slate-400"></i>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada data presensi</p>
            </div>
        </td>
    </tr>
@endforelse