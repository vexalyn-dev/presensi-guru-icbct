@extends('layouts.app')

@section('page-title', 'Edit Jadwal Mengajar - ' . $teacher->name)

@section('content')
    <div class="fade-in space-y-6">

        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('teaching-schedules.index') }}"
                class="p-2.5 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-xl transition-all hover:-translate-x-1 group">
                <i data-lucide="arrow-left"
                    class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-navy-800 dark:group-hover:text-gold-400 transition-colors"></i>
            </a>

            <div class="flex items-center gap-3">
                <img src="{{ $teacher->photo_url }}"
                    class="w-14 h-14 rounded-2xl object-cover border-2 border-white dark:border-slate-700 shadow-lg">
                <div>
                    <h1 class="text-xl font-bold text-navy-800 dark:text-white">{{ $teacher->name }}</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Atur jadwal mengajar per kelas</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('teaching-schedules.update', $teacher) }}" method="POST" id="schedule-form">
            @csrf
            @method('PUT')

            <div class="card p-6">
                <!-- Header Section -->
                <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-11 h-11 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-lucide="calendar-range" class="w-5 h-5 text-white dark:text-navy-900"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-navy-800 dark:text-white">Jadwal Mengajar Mingguan</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Atur kelas, mata pelajaran, dan jam
                                mengajar</p>
                        </div>
                    </div>
                </div>

                <!-- Existing Schedules -->
                @if($schedules->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Jadwal Saat Ini</h3>
                        <div class="space-y-2">
                            @foreach($schedules as $schedule)
                                <div
                                    class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-700/30 rounded-lg border border-slate-200 dark:border-slate-700">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center">
                                        <span class="text-[9px] font-bold text-white dark:text-navy-900 uppercase">
                                            {{ substr(\App\Models\TeachingSchedule::getDayName($schedule->day_of_week), 0, 3) }}
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-bold text-navy-800 dark:text-white">{{ $schedule->classroom->name }}
                                        </p>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400">
                                            Jam {{ $schedule->period }} • {{ $schedule->subject?->name ?? '-' }}
                                        </p>
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Add New Schedule Section -->
                <div id="schedule-container" class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Tambah Jadwal Baru</h3>

                    <!-- Template untuk jadwal baru akan ditambahkan via JavaScript -->
                </div>

                <button type="button" onclick="addScheduleRow()"
                    class="mt-4 w-full px-4 py-3 border-2 border-dashed border-slate-300 dark:border-slate-600 hover:border-navy-800 dark:hover:border-gold-400 text-slate-500 dark:text-slate-400 hover:text-navy-800 dark:hover:text-gold-400 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Jadwal</span>
                </button>

                <!-- Action Buttons -->
                <div class="flex gap-3 mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('teaching-schedules.index') }}"
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
        const classrooms = @json($classrooms);
        const subjects = @json($subjects);
        let scheduleCount = 0;

        function addScheduleRow() {
            const container = document.getElementById('schedule-container');
            const index = scheduleCount++;

            const div = document.createElement('div');
            div.className = 'schedule-row p-4 bg-slate-50 dark:bg-slate-700/30 rounded-xl border border-slate-200 dark:border-slate-700';
            div.innerHTML = `
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Hari</label>
                            <select name="schedules[${index}][day_of_week]" required
                                    class="w-full px-3 py-2.5 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                                <option value="1">Senin</option>
                                <option value="2">Selasa</option>
                                <option value="3">Rabu</option>
                                <option value="4">Kamis</option>
                                <option value="5">Jumat</option>
                                <option value="6">Sabtu</option>
                                <option value="0">Minggu</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kelas</label>
                            <select name="schedules[${index}][classroom_id]" required
                                    class="w-full px-3 py-2.5 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                                <option value="">Pilih Kelas</option>
                                ${classrooms.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Jam Pelajaran</label>
                            <select name="schedules[${index}][period]" required
                                    class="w-full px-3 py-2.5 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                                ${Array.from({ length: 15 }, (_, i) => `<option value="${i + 1}">Jam ${i + 1}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                <i data-lucide="sunrise" class="w-3.5 h-3.5 inline mr-1"></i>
                                Jam Masuk
                            </label>
                            <input type="time" name="schedules[${index}][start_time]" required
                                   value="07:00"
                                   class="w-full px-3 py-2.5 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                <i data-lucide="sunset" class="w-3.5 h-3.5 inline mr-1"></i>
                                Jam Pulang
                            </label>
                            <input type="time" name="schedules[${index}][end_time]" required
                                   value="15:00"
                                   class="w-full px-3 py-2.5 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Mata Pelajaran (Opsional)</label>
                        <select name="schedules[${index}][subject_id]"
                                class="w-full px-3 py-2.5 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                            <option value="">Pilih Mapel</option>
                            ${subjects.map(s => `<option value="${s.id}">${s.name}</option>`).join('')}
                        </select>
                    </div>
                    <button type="button" onclick="this.closest('.schedule-row').remove()" 
                            class="mt-3 px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-xs font-semibold hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors flex items-center gap-1">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        Hapus Jadwal
                    </button>
                `;

            container.appendChild(div);
            if (window.lucide) lucide.createIcons();
        }

        document.addEventListener('DOMContentLoaded', () => {
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
    </style>
@endsection