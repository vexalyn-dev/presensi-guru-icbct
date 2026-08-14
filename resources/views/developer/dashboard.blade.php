@extends('layouts.developer')
@section('content')

{{-- ═══ SECTION: DASHBOARD ═══ --}}
<div id="sec-dashboard" class="space-y-6 fade-in">
    
    {{-- TOP 4 CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        {{-- Card 1: Total Users --}}
        <div class="hc-card p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-full bg-[#f3e8ff] flex items-center justify-center flex-shrink-0">
                <i data-lucide="users" class="w-6 h-6 text-[#9333ea]"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $stats['total_users'] ?? 0 }}</h3>
                <p class="text-sm text-slate-500 font-medium">Total Users</p>
            </div>
            <div class="ml-auto self-start">
                <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-300 cursor-pointer"></i>
            </div>
        </div>

        {{-- Card 2: Total Teachers --}}
        <div class="hc-card p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-full bg-[#e0f2fe] flex items-center justify-center flex-shrink-0">
                <i data-lucide="graduation-cap" class="w-6 h-6 text-[#0ea5e9]"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $stats['total_teachers'] ?? 0 }}</h3>
                <p class="text-sm text-slate-500 font-medium">Active Teachers</p>
            </div>
            <div class="ml-auto self-start">
                <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-300 cursor-pointer"></i>
            </div>
        </div>

        {{-- Card 3: PHP Version --}}
        <div class="hc-card p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-full bg-[#ffedd5] flex items-center justify-center flex-shrink-0">
                <i data-lucide="server" class="w-6 h-6 text-[#f97316]"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $stats['php_version'] ?? '8.x' }}</h3>
                <p class="text-sm text-slate-500 font-medium">PHP Engine</p>
            </div>
            <div class="ml-auto self-start">
                <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-300 cursor-pointer"></i>
            </div>
        </div>

        {{-- Card 4: Laravel Version --}}
        <div class="hc-card p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-full bg-[#ffe4e6] flex items-center justify-center flex-shrink-0">
                <i data-lucide="code-2" class="w-6 h-6 text-[#e11d48]"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $stats['laravel_version'] ?? '10.x' }}</h3>
                <p class="text-sm text-slate-500 font-medium">Framework</p>
            </div>
            <div class="ml-auto self-start">
                <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-300 cursor-pointer"></i>
            </div>
        </div>
    </div>

    {{-- MIDDLE SECTION --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        {{-- Left Large Card (Matches Chart size in UI) --}}
        <div class="xl:col-span-2 hc-card p-6 flex flex-col h-[400px]">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-lg font-bold text-slate-800">System Information & Actions</h2>
                <div class="flex items-center gap-2 text-sm text-slate-500 cursor-pointer font-medium">
                    <span>Show details</span>
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </div>
            </div>
            
            <div class="flex-1 grid grid-cols-2 sm:grid-cols-3 gap-6 content-start">
                @foreach([
                    ['Environment', strtoupper($stats['env']), 'bg-purple-100 text-purple-600'],
                    ['Debug Mode', $stats['debug'] ? 'Enabled' : 'Disabled', 'bg-blue-100 text-blue-600'],
                    ['Host URL', parse_url($stats['app_url'], PHP_URL_HOST) ?? 'localhost', 'bg-orange-100 text-orange-600'],
                    ['Server Time', now()->format('H:i WIB'), 'bg-emerald-100 text-emerald-600'],
                ] as [$label, $val, $color])
                <div class="border-l-4 border-slate-200 pl-4 py-1">
                    <p class="text-sm text-slate-500 font-medium mb-1">{{ $label }}</p>
                    <p class="text-lg font-bold text-slate-800">{{ $val }}</p>
                </div>
                @endforeach
            </div>

            <div class="mt-auto border-t border-slate-100 pt-6 flex gap-4">
                <a href="{{ route('developer.clear-cache',$secret) }}" onclick="return confirm('Clear application cache?')" class="flex items-center gap-2 px-4 py-2 bg-[#f8fafc] hover:bg-[#f1f5f9] rounded-lg text-sm font-semibold text-slate-700 transition-colors">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span> Clear Cache
                </a>
                <a href="{{ url('/run-migrate-secret?key=vexalyn19052009') }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-[#f8fafc] hover:bg-[#f1f5f9] rounded-lg text-sm font-semibold text-slate-700 transition-colors">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Run Migrate
                </a>
            </div>
        </div>

        {{-- Right Donut Card (Matches Donut Chart size in UI) --}}
        <div class="xl:col-span-1 hc-card p-6 h-[400px] flex flex-col items-center justify-center relative">
            <h2 class="text-lg font-bold text-slate-800 absolute top-6 left-6">Maintenance Mode</h2>
            
            @php $mOn = \App\Models\AppSetting::getInstance()->maintenance_mode ?? false; @endphp
            
            <div class="relative w-48 h-48 flex items-center justify-center mt-6">
                {{-- Decorative SVG Circle matching the Donut UI --}}
                <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                    <circle cx="50" cy="50" r="40" fill="none" stroke="#f1f5f9" stroke-width="8"></circle>
                    <circle cx="50" cy="50" r="40" fill="none" stroke="{{ $mOn ? '#f97316' : '#6366f1' }}" stroke-width="8" stroke-dasharray="251.2" stroke-dashoffset="{{ $mOn ? '0' : '62.8' }}" class="transition-all duration-1000"></circle>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-bold text-slate-800">{{ $mOn ? 'ON' : 'OFF' }}</span>
                    <span class="text-xs text-slate-500 font-medium mt-1">Status</span>
                </div>
            </div>

            <div class="w-full mt-auto flex items-center justify-between px-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#f97316]"></span>
                    <span class="text-sm font-medium text-slate-600">Active</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#6366f1]"></span>
                    <span class="text-sm font-medium text-slate-600">Inactive</span>
                </div>
            </div>
        </div>

    </div>

    {{-- BOTTOM SECTION --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- APK Info (Matches Time Admitted chart size) --}}
        <div class="md:col-span-1 hc-card p-6 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-slate-800">APK Package</h2>
                <div class="flex items-center gap-2 text-sm text-slate-500 cursor-pointer font-medium">
                    <span>Details</span>
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </div>
            </div>
            
            @if($appSetting?->apk_file)
            <div class="flex-1 flex flex-col justify-center">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <i data-lucide="package-check" class="w-6 h-6 text-emerald-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800">{{ $appSetting->apk_name ?? 'ICB CT' }}</h4>
                        <p class="text-sm text-slate-500">v{{ $appSetting->apk_version_label ?? '1.0' }}</p>
                    </div>
                </div>
                <p class="text-sm text-slate-500 font-medium border-t border-slate-100 pt-4">{{ $appSetting->apk_size_human ?? '-' }} • Uploaded {{ $appSetting->apk_uploaded_at?->diffForHumans() }}</p>
            </div>
            @else
            <div class="flex-1 flex flex-col items-center justify-center text-center">
                <i data-lucide="package-x" class="w-8 h-8 text-slate-300 mb-2"></i>
                <p class="text-sm text-slate-500 font-medium">No APK uploaded yet</p>
            </div>
            @endif
        </div>

        {{-- Releases List (Matches Patients By Division size) --}}
        <div class="md:col-span-1 hc-card p-6 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-slate-800">Recent Releases</h2>
                <div class="flex items-center gap-2 text-sm text-slate-500 cursor-pointer font-medium">
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </div>
            </div>
            
            <div class="flex-1 flex flex-col gap-4">
                <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                    <div class="flex items-center gap-2"><i data-lucide="history" class="w-3.5 h-3.5"></i> VERSION</div>
                    <span>TYPE</span>
                </div>
                
                @forelse(collect($updates)->take(3) as $u)
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center">
                            <i data-lucide="git-commit" class="w-4 h-4 text-slate-500"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-700">v{{ $u->version }}</span>
                    </div>
                    <span class="text-sm font-bold text-slate-800">{{ ucfirst($u->type) }}</span>
                </div>
                @empty
                <div class="text-center py-4 text-sm text-slate-500">No releases found.</div>
                @endforelse
            </div>
        </div>

        {{-- Right Purple Card (Matches purple gradient card in UI) --}}
        <div class="md:col-span-1 rounded-[16px] p-6 text-white relative overflow-hidden" style="background: linear-gradient(135deg, #7C3AED 0%, #4F46E5 100%);">
            <h3 class="text-3xl font-bold mb-1">ICB CT</h3>
            <p class="text-sm text-white/80 font-medium mb-8">Presensi Guru System</p>
            
            {{-- Decorative graph line matching the UI --}}
            <div class="absolute bottom-4 left-0 w-full h-24 opacity-50 pointer-events-none">
                <svg viewBox="0 0 200 50" class="w-full h-full" preserveAspectRatio="none">
                    <path d="M0,40 Q20,30 40,40 T80,30 T120,40 T160,20 T200,30 L200,50 L0,50 Z" fill="rgba(255,255,255,0.1)"></path>
                    <path d="M0,40 Q20,30 40,40 T80,30 T120,40 T160,20 T200,30" fill="none" stroke="white" stroke-width="2"></path>
                    <circle cx="160" cy="20" r="3" fill="white"></circle>
                </svg>
            </div>
            
            <div class="absolute bottom-6 left-6 right-6 flex justify-between text-xs text-white/60 font-bold">
                <span>Vexalyn Dev</span>
                <span class="text-white">{{ date('Y') }}</span>
            </div>
        </div>

    </div>

</div>

{{-- ═══ SECTION: APK MANAGER (Hidden by default) ═══ --}}
<div id="sec-apk" class="hidden space-y-6 fade-in">
    <div class="hc-card p-8">
        <h2 class="text-xl font-bold text-slate-800 mb-6">Application Packages</h2>
        <form action="{{ route('developer.apk',$secret) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Upload APK File</label>
                <input type="file" name="apk_file" accept=".apk" class="w-full p-4 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">App Name</label>
                    <input type="text" name="apk_name" value="{{ old('apk_name', $appSetting?->apk_name ?? '') }}" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Version</label>
                    <input type="text" name="apk_version" value="{{ old('apk_version', $appSetting?->apk_version ?? '') }}" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>
            <button type="submit" class="btn-primary px-6 py-3">Save APK</button>
        </form>
    </div>
</div>

{{-- ═══ SECTION: MAINTENANCE (Hidden by default) ═══ --}}
<div id="sec-maint" class="hidden space-y-6 fade-in">
    <div class="hc-card p-8">
        <h2 class="text-xl font-bold text-slate-800 mb-6">System State</h2>
        <form action="{{ route('developer.maintenance',$secret) }}" method="POST" class="space-y-6">
            @csrf
            <div class="flex items-center gap-4 p-4 border border-slate-200 rounded-xl bg-slate-50">
                <input type="hidden" name="maintenance_mode" value="0">
                <input type="checkbox" name="maintenance_mode" value="1" {{ $mOn ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded">
                <span class="text-sm font-semibold text-slate-700">Enable Maintenance Mode</span>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Message</label>
                <textarea name="maintenance_message" rows="3" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-indigo-500 outline-none">{{ \App\Models\AppSetting::getInstance()->maintenance_message }}</textarea>
            </div>
            <button type="submit" class="btn-primary px-6 py-3">Update State</button>
        </form>
    </div>
</div>

{{-- ═══ SECTION: UPDATES (Hidden by default) ═══ --}}
<div id="sec-updates" class="hidden space-y-6 fade-in">
    <div class="hc-card p-8">
        <h2 class="text-xl font-bold text-slate-800 mb-6">Release Management</h2>
        <form action="{{ route('developer.updates.store',$secret) }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Version</label>
                    <input type="text" name="version" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-indigo-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Type</label>
                    <select name="type" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="feature">Feature</option>
                        <option value="update">Update</option>
                        <option value="fix">Fix</option>
                        <option value="hotfix">Hotfix</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Title</label>
                <input type="text" name="title" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-indigo-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Changelog</label>
                <textarea name="content" rows="4" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-indigo-500 outline-none" required></textarea>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="show_modal" value="1" checked class="w-5 h-5 text-indigo-600 rounded">
                <span class="text-sm font-semibold text-slate-700">Show Modal on Login</span>
            </div>
            <button type="submit" class="btn-primary px-6 py-3">Publish Release</button>
        </form>
    </div>
</div>

@endsection
