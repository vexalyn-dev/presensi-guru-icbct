@extends('layouts.app')

@section('page-title', 'Edit Jadwal - ' . $teacher->name)

@section('content')
    <div class="fade-in space-y-6">

        <!-- Premium Header -->
        <div class="card p-6 relative overflow-hidden">
            <!-- Gradient Background -->
            <div
                class="absolute inset-0 bg-gradient-to-br from-navy-800/5 via-transparent to-gold-400/5 dark:from-navy-800/20 dark:to-gold-400/10">
            </div>

            <div class="relative flex items-center gap-5">
                <a href="{{ route('schedules.index') }}"
                    class="p-2.5 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-xl transition-all hover:-translate-x-1 group">
                    <i data-lucide="arrow-left"
                        class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-navy-800 dark:group-hover:text-gold-400 transition-colors"></i>
                </a>

                <div class="relative">
                    <img src="{{ $teacher->photo_url }}"
                        class="w-20 h-20 rounded-2xl object-cover border-3 border-white dark:border-slate-700 shadow-xl ring-4 ring-navy-800/10 dark:ring-gold-400/20">
                    <div
                        class="absolute -bottom-1 -right-1 w-7 h-7 bg-gradient-to-br from-green-400 to-emerald-500 border-3 border-white dark:border-slate-800 rounded-full flex items-center justify-center shadow-lg">
                        <i data-lucide="check" class="w-4 h-4 text-white"></i>
                    </div>
                </div>

                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-navy-800 dark:text-white mb-2">{{ $teacher->name }}</h1>
                    <div class="flex items-center gap-3 flex-wrap">
                        @if($teacher->subject)
                            <span
                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 rounded-xl text-indigo-700 dark:text-indigo-300 text-xs font-bold uppercase tracking-wide shadow-sm">
                                <i data-lucide="book-open" class="w-4 h-4"></i>
                                <span>{{ $teacher->subject }}</span>
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 text-sm text-slate-600 dark:text-slate-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                            <span>{{ $teacher->email }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('schedules.update', $teacher) }}" method="POST" id="schedule-form">
            @csrf
            @method('PUT')

            <div class="card p-6">
                <!-- Header Section -->
                <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-11 h-11 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                            <i data-lucide="calendar-clock" class="w-5 h-5 text-white dark:text-navy-900"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-navy-800 dark:text-white">Jadwal Mingguan</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Atur hari dan jam kerja guru</p>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                        <span class="text-xs font-medium text-blue-700 dark:text-blue-300">Centang hari aktif</span>
                    </div>
                </div>

                <!-- Schedule Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="schedule-container">
                    @php
                        // Start from Monday (index 1) to Saturday (index 6), then Sunday (index 0)
                        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                        $dayAbbr = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB', 'MGU'];
                        $dayIndexes = [1, 2, 3, 4, 5, 6, 0]; // Actual day of week indexes
                    @endphp

                    @foreach($days as $index => $day)
                        @php
                            $actualDayIndex = $dayIndexes[$index];
                            $hasSchedule = isset($schedules[$actualDayIndex]);
                        @endphp
                        <div class="schedule-day-card p-5 rounded-2xl border-2 transition-all duration-300 {{ $hasSchedule ? 'bg-gradient-to-br from-navy-800/5 to-gold-400/5 dark:from-navy-800/20 dark:to-gold-400/10 border-navy-800/20 dark:border-gold-400/30 shadow-lg' : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600' }}"
                            data-day-index="{{ $actualDayIndex }}">

                            <!-- Header Row -->
                            <div class="flex items-center gap-3 mb-4">
                                <!-- Day Badge - Uniform Navy/Gold Color -->
                                <div
                                    class="w-14 h-14 rounded-xl bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center shadow-lg flex-shrink-0">
                                    <span class="text-xs font-bold text-white dark:text-navy-900">{{ $dayAbbr[$index] }}</span>
                                </div>

                                <!-- Day Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-bold text-navy-800 dark:text-white truncate">{{ $day }}</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $hasSchedule ? 'Jadwal aktif' : 'Tidak dijadwalkan' }}
                                    </p>
                                </div>

                                <!-- Toggle Switch -->
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="checkbox" id="day_{{ $actualDayIndex }}" class="schedule-toggle sr-only peer"
                                        data-day="{{ $actualDayIndex }}" {{ $hasSchedule ? 'checked' : '' }}>
                                    <div
                                        class="w-12 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-navy-800/20 dark:peer-focus:ring-gold-400/20 rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-gradient-to-r peer-checked:from-navy-800 peer-checked:to-navy-900 dark:peer-checked:from-gold-400 dark:peer-checked:to-gold-500">
                                    </div>
                                </label>
                            </div>

                            <!-- Time Fields -->
                            <div class="grid grid-cols-2 gap-3 schedule-fields {{ $hasSchedule ? '' : 'hidden' }}">
                                <!-- Start Time -->
                                <div class="relative group">
                                    <label
                                        class="flex items-center gap-1.5 text-[10px] font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                        <i data-lucide="clock" class="w-3.5 h-3.5 text-green-500"></i>
                                        <span>Masuk</span>
                                    </label>
                                    <input type="time" name="schedules[{{ $index }}][start_time]"
                                        value="{{ $hasSchedule ? \Carbon\Carbon::parse($schedules[$actualDayIndex]->start_time)->format('H:i') : '07:00' }}"
                                        class="w-full px-3 py-2.5 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all">
                                </div>

                                <!-- End Time -->
                                <div class="relative group">
                                    <label
                                        class="flex items-center gap-1.5 text-[10px] font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                        <i data-lucide="clock" class="w-3.5 h-3.5 text-orange-500"></i>
                                        <span>Pulang</span>
                                    </label>
                                    <input type="time" name="schedules[{{ $index }}][end_time]"
                                        value="{{ $hasSchedule ? \Carbon\Carbon::parse($schedules[$actualDayIndex]->end_time)->format('H:i') : '15:00' }}"
                                        class="w-full px-3 py-2.5 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all">
                                </div>
                            </div>

                            <input type="hidden" name="schedules[{{ $index }}][day_of_week]" value="{{ $actualDayIndex }}">
                            <input type="hidden" name="schedules[{{ $index }}][is_active]"
                                value="{{ $hasSchedule ? '1' : '0' }}">
                        </div>
                    @endforeach
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('schedules.index') }}"
                        class="flex-1 px-6 py-3.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5 text-center flex items-center justify-center gap-2">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        <span>Batal</span>
                    </a>
                    <button type="submit"
                        class="flex-1 px-6 py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Simpan Jadwal</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle schedule fields with animation
            document.querySelectorAll('.schedule-toggle').forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const card = this.closest('.schedule-day-card');
                    const fields = card.querySelector('.schedule-fields');
                    const statusText = card.querySelector('p.text-xs');
                    const hiddenInput = card.querySelector('input[name$="[is_active]"]');

                    if (this.checked) {
                        // Activate
                        fields.classList.remove('hidden');
                        card.classList.remove('bg-slate-50', 'dark:bg-slate-700/30', 'border-slate-200', 'dark:border-slate-700');
                        card.classList.add('bg-gradient-to-br', 'from-navy-800/5', 'to-gold-400/5', 'dark:from-navy-800/20', 'dark:to-gold-400/10', 'border-navy-800/20', 'dark:border-gold-400/30', 'shadow-lg');
                        statusText.textContent = 'Jadwal aktif';
                        if (hiddenInput) hiddenInput.value = '1';
                    } else {
                        // Deactivate
                        fields.classList.add('hidden');
                        card.classList.add('bg-slate-50', 'dark:bg-slate-700/30', 'border-slate-200', 'dark:border-slate-700');
                        card.classList.remove('bg-gradient-to-br', 'from-navy-800/5', 'to-gold-400/5', 'dark:from-navy-800/20', 'dark:to-gold-400/10', 'border-navy-800/20', 'dark:border-gold-400/30', 'shadow-lg');
                        statusText.textContent = 'Tidak dijadwalkan';
                        if (hiddenInput) hiddenInput.value = '0';
                    }
                });
            });

            if (window.lucide) lucide.createIcons();
        });
    </script>

    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .schedule-day-card {
            transition: all 0.3s ease;
        }

        .schedule-day-card:hover {
            transform: translateY(-2px);
        }
    </style>
@endsection