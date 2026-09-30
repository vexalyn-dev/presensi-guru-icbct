@forelse($teachers as $teacher)
    @php
        $idGuru = $teacher->id_guru ?: 'GURU-' . str_pad($teacher->id, 5, '0', STR_PAD_LEFT);
        $numericId = '00000';

        if (preg_match('/GURU-(\d{5})/', $idGuru, $matches)) {
            $numericId = $matches[1];
        } else {
            $numericId = str_pad(preg_replace('/\D/', '', $idGuru), 5, '0', STR_PAD_LEFT);
        }

        $displayCode = 'SMKICBCT-' . $numericId;
        $mapelObjs = optional($teacher->teacher)->subjects ?? collect();
        $mapelNames = $mapelObjs->pluck('name')->filter()->unique()->values();
        $mapel = count($mapelNames) > 0 ? $mapelNames : (optional($teacher->teacher)->major_specialty ?? $teacher->subject);
        $mapel = $mapel instanceof \Illuminate\Support\Collection ? $mapel->join(', ') : ($mapel ?: '');
    @endphp
    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
        <td class="px-4 py-3">
            <label class="tcb-label" style="margin:0;">
                <div class="tcb-box" aria-hidden="true">
                    <svg class="tcb-check" width="13" height="13" viewBox="0 0 13 13" fill="none">
                        <path d="M2 6.5L5 9.5L11 3.5" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}"
                       class="tcb-native teacher-checkbox"
                       onchange="updateBulkActions()">
            </label>
        </td>

        <td class="px-4 py-3 align-middle">
            <span class="text-[11px] font-mono text-slate-500 dark:text-slate-400">#{{ $numericId }}</span>
        </td>

        <td class="px-4 py-3 align-middle">
            <div class="flex items-center gap-3">
                <img src="{{ $teacher->photo_url }}"
                     alt="{{ $teacher->name }}"
                     class="w-10 h-10 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700">
                <div class="flex flex-col gap-0.5">
                    <p class="text-[13px] font-bold text-navy-800 dark:text-white leading-tight">{{ $teacher->name }}</p>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono leading-none">
                        {{ $displayCode }}
                    </p>
                </div>
            </div>
        </td>

        <td class="px-4 py-3 text-center align-middle w-28">
            <span class="text-[13px] text-slate-700 dark:text-slate-300 inline-block">
                {{ $teacher->teacher_code ?? '-' }}
            </span>
        </td>

        <td class="px-4 py-3 text-center align-middle w-64">
            <span class="text-[13px] text-slate-700 dark:text-slate-300 font-medium truncate max-w-[260px] inline-block">
                {{ $teacher->email ?? '-' }}
            </span>
        </td>

        <td class="px-4 py-3 text-center align-middle">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold
                {{ $teacher->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $teacher->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                {{ $teacher->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </td>

        <td class="px-4 py-3 text-center align-middle">
            @if($mapel)
                <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg text-[11px] font-bold uppercase">
                    {{ $mapel }}
                </span>
            @else
                <span class="text-[11px] text-slate-400 italic">-</span>
            @endif
        </td>

        <td class="px-4 py-3 align-middle">
            <div class="flex items-center justify-center gap-2">
                <a href="{{ route('teachers.show', $teacher) }}" class="p-2 w-9 h-9 flex items-center justify-center bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-all" title="Lihat Detail">
                    <i data-lucide="eye" class="w-4 h-4 text-slate-600 dark:text-slate-400"></i>
                </a>
                <a href="{{ route('teachers.edit', $teacher) }}" class="p-2 w-9 h-9 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 rounded-lg transition-all" title="Edit">
                    <i data-lucide="pencil" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                </a>
                <button type="button"
                        class="p-2 w-9 h-9 flex items-center justify-center bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 rounded-lg transition-all delete-btn"
                        data-delete-url="{{ route('teachers.destroy', $teacher) }}"
                        data-delete-label="{{ $teacher->name }}"
                        title="Hapus">
                    <i data-lucide="trash-2" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-4 py-12 text-center">
            <i data-lucide="users" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
            <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada data guru yang ditemukan</p>
        </td>
    </tr>
@endforelse
