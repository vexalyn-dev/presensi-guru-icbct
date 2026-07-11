<div class="overflow-x-auto">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/50">
                <th class="sticky left-0 z-10 bg-slate-50 dark:bg-slate-800/50 px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-r border-slate-200 dark:border-slate-700 min-w-[200px]">Nama</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-r border-slate-200 dark:border-slate-700 min-w-[120px]">Mapel</th>
                @if($reportType === 'class')
                <th class="px-2 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-r border-slate-200 dark:border-slate-700 min-w-[150px]">Kelas</th>
                @endif
                @foreach($dates as $date)
                <th class="px-2 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700 min-w-[45px]">
                    <div>{{ $date->format('d') }}</div>
                    <div class="text-[9px] font-normal text-slate-400">{{ $date->format('D') }}</div>
                </th>
                @endforeach
                <th class="px-3 py-3 text-center text-xs font-semibold text-green-600 dark:text-green-400 border-b border-l-2 border-l-navy-200 dark:border-l-navy-800 border-slate-200 dark:border-slate-700 bg-green-50/50 dark:bg-green-900/10">✓</th>
                <th class="px-3 py-3 text-center text-xs font-semibold text-blue-600 dark:text-blue-400 border-b border-slate-200 dark:border-slate-700 bg-blue-50/50 dark:bg-blue-900/10">I</th>
                <th class="px-3 py-3 text-center text-xs font-semibold text-cyan-600 dark:text-cyan-400 border-b border-slate-200 dark:border-slate-700 bg-cyan-50/50 dark:bg-cyan-900/10">S</th>
                <th class="px-3 py-3 text-center text-xs font-semibold text-red-600 dark:text-red-400 border-b border-slate-200 dark:border-slate-700 bg-red-50/50 dark:bg-red-900/10">✕</th>
                <th class="px-3 py-3 text-center text-xs font-semibold text-yellow-600 dark:text-yellow-400 border-b border-slate-200 dark:border-slate-700 bg-yellow-50/50 dark:bg-yellow-900/10">T</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($reportData as $report)
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <td class="sticky left-0 z-10 bg-white dark:bg-slate-900 px-4 py-3 border-r border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <img src="{{ $report['user']->photo_url }}" 
                             alt="{{ $report['user']->name }}"
                             class="w-8 h-8 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700 flex-shrink-0">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-navy-800 dark:text-white truncate">{{ $report['user']->name }}</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">{{ $report['user']->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 border-r border-slate-200 dark:border-slate-700">
                    <p class="text-xs text-slate-700 dark:text-slate-300">{{ $report['teacher']->major_specialty ?? '-' }}</p>
                </td>
                @if($reportType === 'class')
                <td class="px-2 py-3 border-r border-slate-200 dark:border-slate-700">
                    <p class="text-xs text-slate-700 dark:text-slate-300">
                        @php $firstClass = collect($report['days'])->first(fn($day) => !empty($day['classes'])); @endphp
                        @php $classroom = $firstClass && !empty($firstClass['classes']) ? $firstClass['classes'][0]['classroom'] : '-'; @endphp
                        {{ $classroom }}
                    </p>
                </td>
                @endif
                @foreach($dates as $date)
                @php
                    $dateStr = $date->toDateString();
                    $dayData = $report['days'][$dateStr] ?? ['code' => '-', 'status' => 'libur'];
                    $code = $dayData['code'];
                    $label = $dayData['label'] ?? '';
                    
                    $badgeClass = match($code) {
                        'H' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                        'T' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                        'I' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                        'S' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400',
                        'A' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                        default => 'bg-slate-100 text-slate-400 dark:bg-slate-700 dark:text-slate-500',
                    };
                @endphp
                <td class="px-2 py-3 text-center border-slate-200 dark:border-slate-700 relative group">
                    @if($code === 'H')
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded {{ $badgeClass }}">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </span>
                    @elseif($code === 'A')
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded {{ $badgeClass }}">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </span>
                    @else
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded text-xs font-bold {{ $badgeClass }}">
                            {{ $code }}
                        </span>
                    @endif
                    @if($label && $reportType === 'class')
                        <div class="text-[9px] text-slate-400 mt-0.5">{{ $label }}</div>
                    @endif
                </td>
                @endforeach
                <td class="px-3 py-3 text-center font-bold text-green-600 dark:text-green-400 border-l-2 border-l-navy-200 dark:border-l-navy-800 bg-green-50/30 dark:bg-green-900/10">{{ $report['summary']['H'] }}</td>
                <td class="px-3 py-3 text-center font-bold text-blue-600 dark:text-blue-400 bg-blue-50/30 dark:bg-blue-900/10">{{ $report['summary']['I'] }}</td>
                <td class="px-3 py-3 text-center font-bold text-cyan-600 dark:text-cyan-400 bg-cyan-50/30 dark:bg-cyan-900/10">{{ $report['summary']['S'] }}</td>
                <td class="px-3 py-3 text-center font-bold text-red-600 dark:text-red-400 bg-red-50/30 dark:bg-red-900/10">{{ $report['summary']['A'] }}</td>
                <td class="px-3 py-3 text-center font-bold text-yellow-600 dark:text-yellow-400 bg-yellow-50/30 dark:bg-yellow-900/10">{{ $report['summary']['T'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 2 + ($reportType === 'class' ? 1 : 0) + count($dates) + 5 }}" class="px-6 py-12 text-center">
                    <i data-lucide="inbox" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada data guru</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
