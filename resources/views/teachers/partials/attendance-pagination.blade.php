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
