@if(Auth::user()->isAdmin())
    <p class="px-3 text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Menu Utama
    </p>

    <a href="{{ route('dashboard') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('dashboard')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
        <span>Dashboard</span>
    </a>

    <a href="{{ route('teachers.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('teachers.*')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="users" class="w-4 h-4"></i>
        <span>Data Guru</span>
    </a>

    <a href="{{ route('subjects.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                   {{ request()->routeIs('subjects.*')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="book-open" class="w-4 h-4"></i>
        <span>Mata Pelajaran</span>
    </a>

    {{--

    <a href="{{ route('classes.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('classes.*')
                          ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
                          : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="school" class="w-4 h-4"></i>
        <span>Kelas</span>
    </a>

    <a href="{{ route('schedules.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('schedules.*')
                          ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
                          : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="calendar" class="w-4 h-4"></i>
        <span>Jadwal Mengajar</span>
    </a>
    --}}

    <a href="{{ route('attendance.history') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('attendance.history')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="calendar-days" class="w-4 h-4"></i>
        <span>Riwayat Presensi</span>
    </a>

    <a href="{{ route('attendance.scan') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('attendance.scan')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="qr-code" class="w-4 h-4"></i>
        <span>Absensi</span>
    </a>

    <a href="{{ route('leaves.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('leaves.*')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="file-text" class="w-4 h-4"></i>
        <span>Izin & Sakit</span>
    </a>

    <a href="{{ route('reports.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('reports.*')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="file-bar-chart" class="w-4 h-4"></i>
        <span>Laporan</span>
    </a>

    <a href="{{ route('holidays.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('holidays.*')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="calendar-off" class="w-4 h-4"></i>
        <span>Kalender Libur</span>
    </a>

    {{--
    <a href="{{ route('messages.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('messages.*')
                          ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
                          : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="message-square" class="w-4 h-4"></i>
        <span>Pesan CS</span>
        @php
        $unreadCount = \App\Models\Message::where('is_read', false)
        ->where('sender_id', '!=', Auth::id())
        ->count();
        @endphp
        @if($unreadCount > 0)
        <span class="ml-auto bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $unreadCount }}</span>
        @endif
    </a>
    --}}

    <p class="px-3 text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2 mt-5">Lainnya
    </p>

    <a href="{{ route('settings.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('settings.*') || request()->routeIs('profile.*')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="settings" class="w-4 h-4"></i>
        <span>Pengaturan</span>
    </a>
@else
    {{-- Menu guru (sama gaya dengan admin) --}}
    <p class="px-3 text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Portal Guru
    </p>

    <a href="{{ route('teacher.dashboard') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('teacher.dashboard')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
        <span>Beranda</span>
    </a>


{{--
    <a href="{{ route('teacher.schedule') }}" class="nav-item flex items_center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
                       {{ request()->routeIs('teacher.schedule')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}>
        <i data-lucide="calendar" class="w-4 h-4"></i>
        <span>Jadwal Mengajar</span>
    </a>
--}}

    <a href="{{ route('teacher.attendance') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('teacher.attendance')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="calendar-days" class="w-4 h-4"></i>
        <span>Riwayat Presensi</span>
    </a>

    <a href="{{ route('teacher.profile') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('teacher.profile')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="user" class="w-4 h-4"></i>
        <span>Profil Saya</span>
    </a>

    <a href="{{ route('teacher.leaves.create') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('teacher.leaves.create')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="file-plus" class="w-4 h-4"></i>
        <span>Ajukan Izin / Sakit</span>
    </a>

    <a href="{{ route('teacher.leaves') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      {{ request()->routeIs('teacher.leaves') && !request()->routeIs('teacher.leaves.create')
            ? 'bg-navy-800 text-white shadow-lg shadow-navy-800/30'
            : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
        <i data-lucide="clipboard-list" class="w-4 h-4"></i>
        <span>Riwayat Izin</span>
    </a>
@endif