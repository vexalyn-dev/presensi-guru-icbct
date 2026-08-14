@extends('layouts.developer')
@section('content')

{{-- ══ TAB: DASHBOARD ══ --}}
<div id="tab-dashboard" class="tab-content">
    
    {{-- Top Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="saas-card p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5 text-indigo-600"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-500">Total Users</h3>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] ?? 0 }}</div>
        </div>
        <div class="saas-card p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center">
                    <i data-lucide="graduation-cap" class="w-5 h-5 text-blue-600"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-500">Active Teachers</h3>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['total_teachers'] ?? 0 }}</div>
        </div>
        <div class="saas-card p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-lg bg-orange-50 border border-orange-100 flex items-center justify-center">
                    <i data-lucide="server" class="w-5 h-5 text-orange-600"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-500">PHP Version</h3>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['php_version'] ?? '8.x' }}</div>
        </div>
        <div class="saas-card p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-lg bg-rose-50 border border-rose-100 flex items-center justify-center">
                    <i data-lucide="code-2" class="w-5 h-5 text-rose-600"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-500">Framework</h3>
            </div>
            <div class="text-3xl font-bold text-gray-900">v{{ $stats['laravel_version'] ?? '10.x' }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- System Overview --}}
        <div class="lg:col-span-2 saas-card">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">System Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-y-8 gap-x-12">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Environment</p>
                        <p class="font-medium text-gray-900">{{ strtoupper($stats['env'] ?? 'PRODUCTION') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Debug Mode</p>
                        <p class="font-medium text-gray-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $stats['debug'] ? 'bg-orange-500' : 'bg-gray-300' }}"></span>
                            {{ $stats['debug'] ? 'Enabled (Warning)' : 'Disabled (Safe)' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Host URL</p>
                        <p class="font-medium text-gray-900">{{ parse_url($stats['app_url'] ?? '', PHP_URL_HOST) ?? 'localhost' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Server Time</p>
                        <p class="font-medium text-gray-900">{{ now()->format('H:i WIB') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="lg:col-span-1 saas-card">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Quick Actions</h2>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('developer.clear-cache',$secret) }}" onclick="return confirm('Clear cache?')" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-200">
                    <div class="flex items-center gap-3">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-400"></i>
                        <span class="text-sm font-medium text-gray-700">Clear Cache</span>
                    </div>
                    <i data-lucide="arrow-right" class="w-4 h-4 text-gray-300"></i>
                </a>
                <a href="{{ url('/run-migrate-secret?key=vexalyn19052009') }}" target="_blank" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-200">
                    <div class="flex items-center gap-3">
                        <i data-lucide="database" class="w-4 h-4 text-gray-400"></i>
                        <span class="text-sm font-medium text-gray-700">Run Migrations</span>
                    </div>
                    <i data-lucide="arrow-right" class="w-4 h-4 text-gray-300"></i>
                </a>
            </div>
        </div>
    </div>
</div>


{{-- ══ TAB: APK MANAGER ══ --}}
<div id="tab-apk" class="tab-content">
    <div class="max-w-4xl">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">APK Manager</h2>
            <p class="text-sm text-gray-500 mt-1">Upload and distribute the latest Android build to your users.</p>
        </div>

        @if($appSetting?->apk_file)
        <div class="saas-card mb-8">
            <div class="p-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-green-50 border border-green-100 flex items-center justify-center">
                        <i data-lucide="package-check" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $appSetting->apk_name ?? 'ICB CT Presensi' }} <span class="ml-2 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">v{{ $appSetting->apk_version_label ?? '1.0' }}</span></h4>
                        <p class="text-sm text-gray-500 mt-1">{{ $appSetting->apk_size_human ?? '-' }} • Uploaded {{ $appSetting->apk_uploaded_at?->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ $appSetting->apk_url }}" target="_blank" class="saas-btn-secondary saas-btn">
                        <i data-lucide="download" class="w-4 h-4"></i> Download
                    </a>
                    <form action="{{ route('developer.apk.delete',$secret) }}" method="POST" onsubmit="return confirm('Remove this APK?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="saas-btn" style="background-color: #ef4444;">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="saas-card">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">Upload New Build</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('developer.apk',$secret) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    {{-- File Input --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">APK File</label>
                        <div class="mt-1 flex justify-center rounded-lg border border-dashed border-gray-300 px-6 py-10 hover:bg-gray-50 transition-colors">
                            <div class="text-center">
                                <i data-lucide="upload-cloud" class="mx-auto h-10 w-10 text-gray-300"></i>
                                <div class="mt-4 flex text-sm leading-6 text-gray-600 justify-center">
                                    <label class="relative cursor-pointer rounded-md bg-transparent font-semibold text-indigo-600 focus-within:outline-none hover:text-indigo-500">
                                        <span>Upload a file</span>
                                        <input type="file" name="apk_file" accept=".apk" class="sr-only" onchange="document.getElementById('file-name-display').textContent = this.files[0].name">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p id="file-name-display" class="text-xs leading-5 text-gray-500 mt-1">.apk up to 100MB</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Application Name</label>
                            <input type="text" name="apk_name" value="{{ old('apk_name', $appSetting?->apk_name ?? '') }}" class="saas-input" placeholder="ICB CT Mobile">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Version Label</label>
                            <input type="text" name="apk_version" value="{{ old('apk_version', $appSetting?->apk_version ?? '') }}" class="saas-input" placeholder="1.0.0">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="saas-btn">Save APK Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- ══ TAB: SYSTEM STATE ══ --}}
<div id="tab-system" class="tab-content">
    <div class="max-w-3xl">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">System State</h2>
            <p class="text-sm text-gray-500 mt-1">Control application availability and maintenance mode settings.</p>
        </div>

        <div class="saas-card">
            <form action="{{ route('developer.maintenance',$secret) }}" method="POST">
                @csrf
                @php $mOn = \App\Models\AppSetting::getInstance()->maintenance_mode ?? false; @endphp
                
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Maintenance Mode</h3>
                        <p class="text-sm text-gray-500 mt-1 max-w-md">When active, normal users will see a maintenance page. Admins and Developers can still access the system.</p>
                    </div>
                    <div class="flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="maintenance_mode" value="0">
                            <input type="checkbox" name="maintenance_mode" value="1" {{ $mOn ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                </div>

                <div class="p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance Message (Public)</label>
                    <textarea name="maintenance_message" rows="3" class="saas-input" placeholder="System is currently undergoing maintenance...">{{ \App\Models\AppSetting::getInstance()->maintenance_message }}</textarea>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end rounded-b-xl">
                    <button type="submit" class="saas-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══ TAB: RELEASES ══ --}}
<div id="tab-releases" class="tab-content">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Release History</h2>
        <p class="text-sm text-gray-500 mt-1">Manage changelogs and notify users of new features or fixes.</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        {{-- List --}}
        <div class="xl:col-span-2 space-y-4">
            @forelse($updates as $u)
            <div class="saas-card p-6 flex gap-5 hover:border-gray-300 transition-colors">
                <div class="w-10 h-10 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="{{ $u->type === 'feature' ? 'star' : ($u->type === 'fix' ? 'wrench' : 'git-commit') }}" class="w-5 h-5 text-gray-600"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-3">
                            <h4 class="font-semibold text-gray-900 text-lg">{{ $u->title }}</h4>
                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">v{{ $u->version }}</span>
                        </div>
                        <span class="text-xs text-gray-400">{{ $u->created_at->format('M d, Y') }}</span>
                    </div>
                    <p class="text-sm text-gray-600 whitespace-pre-line mt-3">{{ $u->content }}</p>
                </div>
                <div class="pl-4 border-l border-gray-100 flex flex-col justify-between">
                    <form action="{{ route('developer.updates.delete',[$secret,$u->id]) }}" method="POST" onsubmit="return confirm('Delete this release log?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-gray-400 hover:text-red-500 p-1 transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="saas-card p-12 text-center text-gray-500">
                <i data-lucide="history" class="w-10 h-10 mx-auto mb-3 opacity-20"></i>
                <p>No release history available.</p>
            </div>
            @endforelse
        </div>

        {{-- Form --}}
        <div class="xl:col-span-1">
            <div class="saas-card sticky top-24">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">Create New Release</h3>
                </div>
                <div class="p-5">
                    <form action="{{ route('developer.updates.store',$secret) }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Version</label>
                            <input type="text" name="version" class="saas-input py-2" required placeholder="1.2.0">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                            <select name="type" class="saas-input py-2">
                                <option value="feature">Feature</option>
                                <option value="update">Update</option>
                                <option value="fix">Fix</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" name="title" class="saas-input py-2" required placeholder="Added New Reports">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Changelog</label>
                            <textarea name="content" rows="4" class="saas-input py-2" required placeholder="- Fixed bug A&#10;- Added feature B"></textarea>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="show_modal" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            <label class="text-xs text-gray-600">Show welcome modal to users</label>
                        </div>
                        <button type="submit" class="saas-btn w-full">Publish Release</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
