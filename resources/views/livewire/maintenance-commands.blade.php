{{-- Maintenance Commands --}}
<div class="space-y-6">

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- System Information                                          --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}


    <div class="grid gap-6 md:grid-cols-2">
    <div class="p-6 bg-white border border-gray-200 shadow-sm md:grid-cols-2 rounded-xl dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center gap-3 mb-5">
            <div class="flex items-center justify-center w-10 h-10 bg-indigo-100 rounded-lg dark:bg-indigo-900/40">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <h4 class="text-base font-semibold text-gray-900 dark:text-white">System Information</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Live environment snapshot</p>
            </div>
        </div>

        {{-- Row 1 — Application --}}
        <p class="mb-2 text-xs font-semibold tracking-widest text-gray-400 uppercase dark:text-gray-500">Application</p>
        <div class="grid grid-cols-2 mb-5 gap-x-8 gap-y-3 md:grid-cols-4">
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Laravel</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">v{{ $systemInfo['laravel_version'] }}</span>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">PHP</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $systemInfo['php_version'] }}</span>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Environment</span>
                @if ($systemInfo['environment'] === 'production')
                    <span class="inline-flex w-fit px-2 py-0.5 text-xs font-medium text-red-800 bg-red-100 rounded-full dark:bg-red-900/50 dark:text-red-300">Production</span>
                @elseif($systemInfo['environment'] === 'local')
                    <span class="inline-flex w-fit px-2 py-0.5 text-xs font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900/50 dark:text-green-300">Local</span>
                @else
                    <span class="inline-flex w-fit px-2 py-0.5 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900/50 dark:text-yellow-300">{{ $systemInfo['environment'] }}</span>
                @endif
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Timezone</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $systemInfo['timezone'] }}</span>
            </div>
        </div>

        {{-- Row 2 — Status --}}
        <p class="mb-2 text-xs font-semibold tracking-widest text-gray-400 uppercase dark:text-gray-500">Status</p>
        <div class="grid grid-cols-2 mb-5 gap-x-8 gap-y-3 md:grid-cols-4">
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Debug Mode</span>
                @if ($systemInfo['debug_mode'])
                    <span class="inline-flex w-fit px-2 py-0.5 text-xs font-medium text-red-800 bg-red-100 rounded-full dark:bg-red-900/50 dark:text-red-300">Enabled</span>
                @else
                    <span class="inline-flex w-fit px-2 py-0.5 text-xs font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900/50 dark:text-green-300">Disabled</span>
                @endif
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Maintenance</span>
                @if ($systemInfo['maintenance_mode'])
                    <span class="inline-flex w-fit px-2 py-0.5 text-xs font-medium text-red-800 bg-red-100 rounded-full dark:bg-red-900/50 dark:text-red-300">Active</span>
                @else
                    <span class="inline-flex w-fit px-2 py-0.5 text-xs font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900/50 dark:text-green-300">Inactive</span>
                @endif
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Queue</span>
                <span class="text-sm font-semibold text-gray-900 capitalize dark:text-white">{{ $systemInfo['queue_driver'] }}</span>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Cache</span>
                <span class="text-sm font-semibold text-gray-900 capitalize dark:text-white">{{ $systemInfo['cache_driver'] }}</span>
            </div>
        </div>

        {{-- Row 3 — Infrastructure --}}
        <p class="mb-2 text-xs font-semibold tracking-widest text-gray-400 uppercase dark:text-gray-500">Infrastructure</p>
        <div class="grid grid-cols-2 mb-5 gap-x-8 gap-y-3 md:grid-cols-4">
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">DB Driver</span>
                <span class="text-sm font-semibold text-gray-900 capitalize dark:text-white">{{ $systemInfo['db_driver'] }}</span>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Database</span>
                <span class="text-sm font-semibold text-gray-900 truncate dark:text-white">{{ $systemInfo['db_database'] }}</span>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Session</span>
                <span class="text-sm font-semibold text-gray-900 capitalize dark:text-white">{{ $systemInfo['session_driver'] }}</span>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Mail</span>
                <span class="text-sm font-semibold text-gray-900 capitalize dark:text-white">{{ $systemInfo['mail_mailer'] }}</span>
            </div>
        </div>

        {{-- Row 4 — Server --}}
        <p class="mb-2 text-xs font-semibold tracking-widest text-gray-400 uppercase dark:text-gray-500">Server</p>
        <div class="grid grid-cols-2 mb-5 gap-x-8 gap-y-3 md:grid-cols-4">
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">OS</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $systemInfo['server_os'] }}</span>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">PHP Memory</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $systemInfo['php_memory_limit'] }}</span>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Extensions</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $systemInfo['php_extensions'] }} loaded</span>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Web Server</span>
                <span class="text-sm font-semibold text-gray-900 truncate dark:text-white">{{ $systemInfo['server_software'] }}</span>
            </div>
        </div>

        {{-- Storage / disk bars --}}
        <div class="grid grid-cols-1 gap-3 pt-4 border-t border-gray-100 dark:border-gray-700 sm:grid-cols-2">
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Log File</span>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $logSize }} MB</span>
                </div>
                <div class="w-full h-1.5 bg-gray-200 rounded-full dark:bg-gray-700">
                    <div class="h-1.5 bg-rose-500 rounded-full transition-all" style="width: {{ min($logSize * 10, 100) }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Cache Storage</span>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $cacheSize }} MB</span>
                </div>
                <div class="w-full h-1.5 bg-gray-200 rounded-full dark:bg-gray-700">
                    <div class="h-1.5 bg-blue-500 rounded-full transition-all" style="width: {{ min($cacheSize * 5, 100) }}%"></div>
                </div>
            </div>
            @if ($systemInfo['disk_total_gb'])
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Disk Usage</span>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                        {{ $systemInfo['disk_free_gb'] }} GB free / {{ $systemInfo['disk_total_gb'] }} GB
                        <span class="text-gray-400">({{ $systemInfo['disk_used_pct'] }}%)</span>
                    </span>
                </div>
                <div class="w-full h-1.5 bg-gray-200 rounded-full dark:bg-gray-700">
                    <div class="h-1.5 rounded-full transition-all
                        {{ $systemInfo['disk_used_pct'] >= 90 ? 'bg-red-500' : ($systemInfo['disk_used_pct'] >= 70 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                        style="width: {{ $systemInfo['disk_used_pct'] }}%"></div>
                </div>
            </div>
            @endif
        </div>
    </div>

      {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Cache Management                                            --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}

    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center gap-3 mb-5">
            <div class="flex items-center justify-center w-10 h-10 bg-orange-100 rounded-lg dark:bg-orange-900/40">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <h4 class="text-base font-semibold text-gray-900 dark:text-white">Cache Management</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Clear cached data or build production caches</p>
            </div>
        </div>

        {{-- Clear Caches row --}}
        <p class="mb-2 text-xs font-semibold tracking-widest text-gray-400 uppercase dark:text-gray-500">Clear</p>
        <div class="grid grid-cols-2 gap-3 mb-4 sm:grid-cols-4">
            @php
                $clearButtons = [
                    ['method' => 'clearAppCache',    'label' => 'App Cache',    'cmd' => 'cache:clear',  'color' => 'red'],
                    ['method' => 'clearConfigCache', 'label' => 'Config Cache', 'cmd' => 'config:clear', 'color' => 'amber'],
                    ['method' => 'clearRouteCache',  'label' => 'Route Cache',  'cmd' => 'route:clear',  'color' => 'emerald'],
                    ['method' => 'clearViewCache',   'label' => 'View Cache',   'cmd' => 'view:clear',   'color' => 'blue'],
                ];
            @endphp
            @foreach ($clearButtons as $btn)
                <button wire:click="{{ $btn['method'] }}" wire:loading.attr="disabled"
                    class="group relative flex flex-col items-center gap-1.5 px-3 py-3 text-xs font-medium rounded-lg border transition-all disabled:opacity-50
                        text-{{ $btn['color'] }}-700 border-{{ $btn['color'] }}-200 bg-{{ $btn['color'] }}-50 hover:bg-{{ $btn['color'] }}-100
                        dark:text-{{ $btn['color'] }}-300 dark:border-{{ $btn['color'] }}-800 dark:bg-{{ $btn['color'] }}-900/20 dark:hover:bg-{{ $btn['color'] }}-900/40">
                    <span wire:loading.remove wire:target="{{ $btn['method'] }}">{{ $btn['label'] }}</span>
                    <span wire:loading wire:target="{{ $btn['method'] }}" class="flex items-center gap-1">
                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Running
                    </span>
                    <code class="font-mono text-gray-400 dark:text-gray-500" style="font-size:10px">php artisan {{ $btn['cmd'] }}</code>
                </button>
            @endforeach
        </div>
        <button wire:click="clearAllCaches" wire:loading.attr="disabled"
            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white rounded-lg bg-gradient-to-r from-red-600 to-orange-500 hover:from-red-500 hover:to-orange-400 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50 mb-5">
            <span wire:loading.remove wire:target="clearAllCaches">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" /></svg>
            </span>
            <span wire:loading wire:target="clearAllCaches"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
            <span wire:loading.remove wire:target="clearAllCaches">Clear All Caches</span>
            <span wire:loading wire:target="clearAllCaches">Clearing...</span>
        </button>

        {{-- Build Caches row --}}
        <p class="mb-2 text-xs font-semibold tracking-widest text-gray-400 uppercase dark:text-gray-500">Build</p>
        <div class="grid grid-cols-2 gap-3 mb-4 sm:grid-cols-4">
            @php
                $buildButtons = [
                    ['method' => 'cacheConfig', 'label' => 'Cache Config', 'cmd' => 'config:cache', 'color' => 'cyan'],
                    ['method' => 'cacheRoutes', 'label' => 'Cache Routes', 'cmd' => 'route:cache',  'color' => 'teal'],
                    ['method' => 'cacheViews',  'label' => 'Cache Views',  'cmd' => 'view:cache',   'color' => 'sky'],
                ];
            @endphp
            @foreach ($buildButtons as $btn)
                <button wire:click="{{ $btn['method'] }}" wire:loading.attr="disabled"
                    class="flex flex-col items-center gap-1.5 px-3 py-3 text-xs font-medium rounded-lg border transition-all disabled:opacity-50
                        text-{{ $btn['color'] }}-700 border-{{ $btn['color'] }}-200 bg-{{ $btn['color'] }}-50 hover:bg-{{ $btn['color'] }}-100
                        dark:text-{{ $btn['color'] }}-300 dark:border-{{ $btn['color'] }}-800 dark:bg-{{ $btn['color'] }}-900/20 dark:hover:bg-{{ $btn['color'] }}-900/40">
                    <span wire:loading.remove wire:target="{{ $btn['method'] }}">{{ $btn['label'] }}</span>
                    <span wire:loading wire:target="{{ $btn['method'] }}" class="flex items-center gap-1">
                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Running
                    </span>
                    <code class="font-mono text-gray-400 dark:text-gray-500" style="font-size:10px">php artisan {{ $btn['cmd'] }}</code>
                </button>
            @endforeach
            <button wire:click="cacheAll" wire:loading.attr="disabled"
                class="flex flex-col items-center gap-1.5 px-3 py-3 text-xs font-medium text-white rounded-lg border border-transparent bg-gradient-to-br from-cyan-600 to-teal-600 hover:from-cyan-500 hover:to-teal-500 disabled:opacity-50 transition-all">
                <span wire:loading.remove wire:target="cacheAll">Cache All</span>
                <span wire:loading wire:target="cacheAll" class="flex items-center gap-1"><svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Running</span>
                <code class="font-mono text-cyan-200" style="font-size:10px">config+route+view</code>
            </button>
        </div>
    </div>

    </div>


    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Quick Actions + Database (side by side)                     --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="grid gap-6 md:grid-cols-2">

        {{-- Quick Actions --}}
        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center gap-3 mb-5">
                <div class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-lg dark:bg-purple-900/40">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h4 class="text-base font-semibold text-gray-900 dark:text-white">Quick Actions</h4>
            </div>

            <div class="space-y-2">
                {{-- Optimize --}}
                <button wire:click="optimize" wire:loading.attr="disabled"
                    class="inline-flex items-center w-full gap-3 px-4 py-3 text-sm font-medium text-green-700 transition-all border border-green-200 rounded-lg bg-green-50 hover:bg-green-100 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300 dark:hover:bg-green-900/40 disabled:opacity-50">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" /></svg>
                    <span wire:loading.remove wire:target="optimize">Optimize Application</span>
                    <span wire:loading wire:target="optimize">Optimizing...</span>
                </button>

                {{-- Storage Link --}}
                <button wire:click="storageLink" wire:loading.attr="disabled"
                    class="inline-flex items-center w-full gap-3 px-4 py-3 text-sm font-medium text-gray-700 transition-all bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white dark:hover:bg-gray-700 disabled:opacity-50">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" /></svg>
                    Create Storage Link
                </button>

                {{-- Maintenance Toggle --}}
                <button wire:click="toggleMaintenance" wire:loading.attr="disabled"
                    class="w-full inline-flex items-center gap-3 px-4 py-3 text-sm font-medium border rounded-lg transition-all disabled:opacity-50
                        {{ $systemInfo['maintenance_mode']
                            ? 'text-green-700 border-green-200 bg-green-50 hover:bg-green-100 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300 dark:hover:bg-green-900/40'
                            : 'text-amber-700 border-amber-200 bg-amber-50 hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-300 dark:hover:bg-amber-900/40' }}">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                    @if ($systemInfo['maintenance_mode'])
                        Disable Maintenance Mode
                    @else
                        Enable Maintenance Mode
                    @endif
                </button>

                {{-- View Env --}}
                <button wire:click="viewEnv" wire:loading.attr="disabled"
                    class="inline-flex items-center w-full gap-3 px-4 py-3 text-sm font-medium text-gray-700 transition-all bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white dark:hover:bg-gray-700 disabled:opacity-50">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    View .env File
                </button>

                {{-- Run Schedule --}}
                <button wire:click="runSchedule" wire:loading.attr="disabled"
                    class="inline-flex items-center w-full gap-3 px-4 py-3 text-sm font-medium text-blue-700 transition-all border border-blue-200 rounded-lg bg-blue-50 hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300 dark:hover:bg-blue-900/40 disabled:opacity-50">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>
                    <span wire:loading.remove wire:target="runSchedule">Run Scheduler</span>
                    <span wire:loading wire:target="runSchedule">Running...</span>
                </button>
            </div>
        </div>

        {{-- Database Commands --}}
        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center gap-3 mb-5">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h4 class="text-base font-semibold text-gray-900 dark:text-white">Database Commands</h4>
            </div>

            <div class="space-y-2">
                {{-- Check migrations --}}
                <button wire:click="checkMigrations" wire:loading.attr="disabled"
                    class="inline-flex items-center justify-between w-full px-4 py-3 text-sm font-medium text-gray-700 transition-all bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white dark:hover:bg-gray-700 disabled:opacity-50">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-300 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" /></svg>
                        <span wire:loading.remove wire:target="checkMigrations">Migration Status</span>
                        <span wire:loading wire:target="checkMigrations">Checking...</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                </button>

                {{-- Run migrations --}}
                <button wire:click="runMigrations" wire:loading.attr="disabled"
                    class="inline-flex items-center justify-between w-full px-4 py-3 text-sm font-medium transition-all border rounded-lg text-emerald-700 border-emerald-200 bg-emerald-50 hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300 dark:hover:bg-emerald-900/40 disabled:opacity-50">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        <span wire:loading.remove wire:target="runMigrations">Run Migrations</span>
                        <span wire:loading wire:target="runMigrations">Migrating...</span>
                    </div>
                    <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                </button>

                {{-- Fresh Seed --}}
                <button x-data x-on:click="
                        if (confirm('⚠️ WARNING: This will DROP ALL TABLES and reseed. This cannot be undone!\n\nContinue?')) {
                            $wire.call('migrateFreshSeed');
                        }
                    "
                    class="inline-flex items-center justify-between w-full px-4 py-3 text-sm font-medium text-red-700 transition-all border border-red-200 rounded-lg bg-red-50 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300 dark:hover:bg-red-900/40">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        <span>Fresh Migration + Seed</span>
                        <span class="text-xs opacity-70">(drops all tables)</span>
                    </div>
                    <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Queue Management                                            --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center gap-3 mb-5">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-violet-100 dark:bg-violet-900/40">
                <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                </svg>
            </div>
            <h4 class="text-base font-semibold text-gray-900 dark:text-white">Queue Management</h4>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
            {{-- Stats --}}
            <button wire:click="getQueueStats" wire:loading.attr="disabled"
                class="inline-flex flex-col items-center gap-2 px-4 py-4 text-sm font-medium text-gray-700 transition-all bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white dark:hover:bg-gray-700 disabled:opacity-50">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" /></svg>
                <span wire:loading.remove wire:target="getQueueStats">Statistics</span>
                <span wire:loading wire:target="getQueueStats">Loading...</span>
            </button>

            {{-- Restart --}}
            <button wire:click="restartQueue" wire:loading.attr="disabled"
                class="inline-flex flex-col items-center gap-2 px-4 py-4 text-sm font-medium text-blue-700 transition-all border border-blue-200 rounded-lg bg-blue-50 hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300 dark:hover:bg-blue-900/40 disabled:opacity-50">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" /></svg>
                <span wire:loading.remove wire:target="restartQueue">Restart Workers</span>
                <span wire:loading wire:target="restartQueue">Sending...</span>
            </button>

            {{-- Check Status --}}
            <button wire:click="checkQueueStatus" wire:loading.attr="disabled"
                class="inline-flex flex-col items-center gap-2 px-4 py-4 text-sm font-medium text-green-700 transition-all border border-green-200 rounded-lg bg-green-50 hover:bg-green-100 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300 dark:hover:bg-green-900/40 disabled:opacity-50">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                <span wire:loading.remove wire:target="checkQueueStatus">Check Status</span>
                <span wire:loading wire:target="checkQueueStatus">Checking...</span>
            </button>

            {{-- Retry Failed --}}
            <button wire:click="retryFailedJobs" wire:loading.attr="disabled"
                class="inline-flex flex-col items-center gap-2 px-4 py-4 text-sm font-medium transition-all border rounded-lg disabled:opacity-50 text-amber-700 border-amber-200 bg-amber-50 hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-300 dark:hover:bg-amber-900/40">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" /></svg>
                <span wire:loading.remove wire:target="retryFailedJobs">Retry Failed</span>
                <span wire:loading wire:target="retryFailedJobs">Retrying...</span>
            </button>

            {{-- Flush Failed --}}
            <button x-data x-on:click="
                    if (confirm('Permanently delete ALL failed jobs? This cannot be undone.')) {
                        $wire.call('flushFailedJobs');
                    }
                "
                class="inline-flex flex-col items-center gap-2 px-4 py-4 text-sm font-medium text-red-700 transition-all border border-red-200 rounded-lg bg-red-50 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300 dark:hover:bg-red-900/40">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                Flush Failed Jobs
            </button>

            {{-- Daemon Info --}}
            <button wire:click="startDaemonQueue"
                class="inline-flex flex-col items-center gap-2 px-4 py-4 text-sm font-medium text-purple-700 transition-all border border-purple-200 rounded-lg bg-purple-50 hover:bg-purple-100 dark:border-purple-800 dark:bg-purple-900/20 dark:text-purple-300 dark:hover:bg-purple-900/40">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" /></svg>
                Daemon Info
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Session Management                                          --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center gap-3 mb-5">
            <div class="flex items-center justify-center w-10 h-10 bg-teal-100 rounded-lg dark:bg-teal-900/40">
                <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <h4 class="text-base font-semibold text-gray-900 dark:text-white">Session Management</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Guest session lifecycle control</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <button wire:click="showGuestSessionStats" wire:loading.attr="disabled"
                class="inline-flex items-center gap-3 px-4 py-3 text-sm font-medium text-teal-700 transition-all border border-teal-200 rounded-lg bg-teal-50 hover:bg-teal-100 dark:border-teal-800 dark:bg-teal-900/20 dark:text-teal-300 dark:hover:bg-teal-900/40 disabled:opacity-50">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" /></svg>
                <span wire:loading.remove wire:target="showGuestSessionStats">Session Statistics</span>
                <span wire:loading wire:target="showGuestSessionStats">Loading...</span>
            </button>

            <button x-data x-on:click="
                    if (confirm('Mark expired sessions and delete old ones older than 1 day. Continue?')) {
                        $wire.call('clearExpiredSessions');
                    }
                "
                class="inline-flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-700 transition-all border border-red-200 rounded-lg bg-red-50 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300 dark:hover:bg-red-900/40">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                Clear Expired Sessions
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Log Viewer                                                   --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-rose-100 dark:bg-rose-900/40">
                    <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">Log Viewer</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">laravel.log &mdash; <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $logSize }} MB</span></p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Line presets --}}
                @foreach ([25, 50, 100, 200, 500, 1000] as $preset)
                    <button wire:click="$set('logLines', {{ $preset }})"
                        class="px-2.5 py-1 text-xs font-medium rounded-full border transition-all
                            {{ (int)$logLines === $preset
                                ? 'bg-rose-600 text-white border-rose-600'
                                : 'text-gray-600 border-gray-300 hover:bg-gray-100 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700' }}">
                        {{ $preset }}
                    </button>
                @endforeach

                <div class="w-px h-5 mx-1 bg-gray-200 dark:bg-gray-600"></div>

                {{-- View Logs --}}
                <button wire:click="viewLogs" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-rose-600 border border-rose-600 rounded-lg hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500 disabled:opacity-50 transition-all">
                    <span wire:loading.remove wire:target="viewLogs">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                    </span>
                    <span wire:loading wire:target="viewLogs"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                    <span wire:loading.remove wire:target="viewLogs">View</span>
                    <span wire:loading wire:target="viewLogs">Loading…</span>
                </button>

                {{-- Clear Logs --}}
                <button x-data x-on:click="
                        if (confirm('Clear laravel.log? This cannot be undone.')) {
                            $wire.call('clearLogs');
                        }
                    "
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 transition-all">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                    Clear
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Command Output                                              --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div x-data="{
            copied: false,
            copyOutput() {
                const text = document.getElementById('terminal-output').innerText;
                navigator.clipboard.writeText(text).then(() => {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                });
            }
        }"
        class="overflow-hidden border border-gray-800 shadow-2xl rounded-xl bg-gray-950">

        {{-- Title bar --}}
        <div class="flex items-center justify-between px-4 py-2.5 bg-gray-900 border-b border-gray-800">
            {{-- Traffic lights --}}
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 bg-red-500 rounded-full hover:bg-red-400 transition-colors cursor-default" title="Close"></span>
                <span class="w-3 h-3 bg-yellow-400 rounded-full hover:bg-yellow-300 transition-colors cursor-default" title="Minimize"></span>
                <span class="w-3 h-3 bg-green-500 rounded-full hover:bg-green-400 transition-colors cursor-default" title="Maximize"></span>
            </div>

            {{-- Path breadcrumb --}}
            <div class="flex items-center gap-1.5 font-mono text-xs text-gray-500 select-none">
                <svg class="w-3.5 h-3.5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-600">~/</span><span class="text-emerald-400">laravel</span>
                <span class="text-gray-600 mx-0.5">›</span>
                <span class="text-sky-400">php artisan</span>
            </div>

            {{-- Right actions --}}
            <div class="flex items-center gap-2">
                {{-- Copy button --}}
                <button @click="copyOutput()" :disabled="!$wire.output"
                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-md transition-all
                        text-gray-400 hover:text-white hover:bg-gray-700 disabled:opacity-30 disabled:cursor-not-allowed">
                    <template x-if="!copied">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"/><path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"/></svg>
                    </template>
                    <template x-if="copied">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </template>
                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                </button>

                <div class="w-px h-4 bg-gray-700"></div>

                {{-- Clear button --}}
                <button wire:click="$set('output', '')" :disabled="!$wire.output"
                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-md transition-all
                        text-gray-400 hover:text-red-400 hover:bg-gray-700 disabled:opacity-30 disabled:cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                    Clear
                </button>
            </div>
        </div>

        {{-- Gutter + output --}}
        <div class="flex">
            {{-- Line-number gutter --}}
            <div class="hidden sm:flex flex-col items-end pt-5 pb-5 pl-3 pr-3 bg-gray-900/50 border-r border-gray-800 select-none min-w-[2.75rem]"
                aria-hidden="true">
                @if ($output)
                    @php $lineCount = substr_count($output, "\n") + 1; @endphp
                    @for ($i = 1; $i <= min($lineCount, 200); $i++)
                        <span class="font-mono text-gray-700 leading-relaxed" style="font-size:11px">{{ $i }}</span>
                    @endfor
                @else
                    <span class="font-mono text-gray-700 leading-relaxed" style="font-size:11px">1</span>
                @endif
            </div>

            {{-- Output body --}}
            <div class="flex-1 overflow-x-auto overflow-y-auto max-h-[28rem] p-5" id="terminal-output"
                wire:key="terminal-output-{{ strlen($output) }}">
                @if ($output)
                    @php
                        $lines = explode("\n", $output);
                    @endphp
                    @foreach ($lines as $line)
                        @php
                            $trimmed = ltrim($line);
                            if (str_starts_with($trimmed, '✅') || str_starts_with($trimmed, '🎉') || str_starts_with($trimmed, '🔗')) {
                                $cls = 'text-emerald-400';
                            } elseif (str_starts_with($trimmed, '❌')) {
                                $cls = 'text-red-400';
                            } elseif (str_starts_with($trimmed, '⚠️') || str_starts_with($trimmed, '⚙️') || str_starts_with($trimmed, '🔧') || str_starts_with($trimmed, '🚀')) {
                                $cls = 'text-amber-400';
                            } elseif (str_starts_with($trimmed, 'Running:')) {
                                $cls = 'text-sky-400';
                            } elseif (str_starts_with($trimmed, 'php artisan') || str_starts_with($trimmed, '  php artisan')) {
                                $cls = 'text-violet-400';
                            } elseif (str_starts_with($trimmed, '#')) {
                                $cls = 'text-gray-600';
                            } else {
                                $cls = 'text-gray-300';
                            }
                        @endphp
                        <div class="font-mono leading-relaxed {{ $cls }}" style="font-size:13px">{{ $line ?: '&nbsp;' }}</div>
                    @endforeach
                @else
                    <div class="flex flex-col items-center justify-center h-40 gap-3 select-none">
                        <svg class="w-10 h-10 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="font-mono text-sm text-gray-600">Run a command above — output will appear here.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Status bar --}}
        <div class="flex items-center justify-between px-4 py-1.5 bg-gray-900/60 border-t border-gray-800 select-none">
            <div class="flex items-center gap-3 font-mono text-gray-600" style="font-size:11px">
                @if ($output)
                    @php $lc = substr_count($output, "\n") + 1; @endphp
                    <span>{{ $lc }} {{ Str::plural('line', $lc) }}</span>
                    <span>·</span>
                    <span>{{ strlen($output) }} bytes</span>
                @else
                    <span>empty</span>
                @endif
            </div>
            <div class="flex items-center gap-1.5 font-mono text-gray-600" style="font-size:11px">
                <span class="inline-block w-1.5 h-1.5 rounded-full {{ $output ? 'bg-emerald-500' : 'bg-gray-600' }}"></span>
                <span>{{ $output ? 'ready' : 'idle' }}</span>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Command History                                             --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if (count($commandHistory) > 0)
        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-indigo-100 rounded-lg dark:bg-indigo-900/40">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">Command History <span class="ml-1 text-sm font-normal text-gray-400">(last {{ count($commandHistory) }})</span></h4>
                </div>
                <button wire:click="clearHistory"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-all">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                    Clear History
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="pb-2 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Command</th>
                            <th class="pb-2 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Timestamp</th>
                            <th class="pb-2 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Time</th>
                            <th class="pb-2 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @foreach (array_reverse($commandHistory) as $history)
                            <tr class="transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                <td class="py-2.5 pr-4 text-sm">
                                    <code class="font-mono text-xs text-gray-700 dark:text-gray-300">php artisan {{ $history['command'] }}</code>
                                </td>
                                <td class="py-2.5 pr-4 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $history['timestamp'] }}</td>
                                <td class="py-2.5 pr-4 text-xs text-gray-500 dark:text-gray-400">{{ $history['execution_time'] }}s</td>
                                <td class="py-2.5">
                                    @if ($history['status'] === 'success')
                                        <span class="inline-flex px-2 py-0.5 text-xs font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900/50 dark:text-green-300">Success</span>
                                    @elseif($history['status'] === 'error')
                                        <span class="inline-flex px-2 py-0.5 text-xs font-medium text-red-800 bg-red-100 rounded-full dark:bg-red-900/50 dark:text-red-300">Error</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900/50 dark:text-yellow-300">Warning</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
