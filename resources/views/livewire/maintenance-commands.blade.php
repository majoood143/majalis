{{-- Maintenance Commands --}}
<div class="space-y-6">

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- System Information                                          --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}

    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <x-filament::icon icon="heroicon-s-information-circle" class="w-5 h-5 text-primary-500" />
                System Information
            </div>
        </x-slot>
        <x-slot name="description">
            Live environment snapshot
        </x-slot>

        {{-- Row 1 — Application --}}
        <p class="mb-2 text-xs font-semibold tracking-widest text-gray-400 uppercase dark:text-gray-500">Application</p>
        <div class="grid grid-cols-2 mb-5 gap-x-8 gap-y-3 md:grid-cols-4">
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Laravel</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">v{{ $systemInfo['laravel_version'] }}</span>
            </div>
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">PHP</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $systemInfo['php_version'] }}</span>
            </div>
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Environment</span>
                <x-filament::badge :color="$systemInfo['environment'] === 'production' ? 'danger' : ($systemInfo['environment'] === 'local' ? 'success' : 'warning')" class="w-fit">
                    {{ ucfirst($systemInfo['environment']) }}
                </x-filament::badge>
            </div>
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Timezone</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $systemInfo['timezone'] }}</span>
            </div>
        </div>

        {{-- Row 2 — Status --}}
        <p class="mb-2 text-xs font-semibold tracking-widest text-gray-400 uppercase dark:text-gray-500">Status</p>
        <div class="grid grid-cols-2 mb-5 gap-x-8 gap-y-3 md:grid-cols-4">
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Debug Mode</span>
                <x-filament::badge :color="$systemInfo['debug_mode'] ? 'danger' : 'success'" class="w-fit">
                    {{ $systemInfo['debug_mode'] ? 'Enabled' : 'Disabled' }}
                </x-filament::badge>
            </div>
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Maintenance</span>
                <x-filament::badge :color="$systemInfo['maintenance_mode'] ? 'danger' : 'success'" class="w-fit">
                    {{ $systemInfo['maintenance_mode'] ? 'Active' : 'Inactive' }}
                </x-filament::badge>
            </div>
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Queue</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst($systemInfo['queue_driver']) }}</span>
            </div>
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Cache</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst($systemInfo['cache_driver']) }}</span>
            </div>
        </div>

        {{-- Row 3 — Infrastructure --}}
        <p class="mb-2 text-xs font-semibold tracking-widest text-gray-400 uppercase dark:text-gray-500">Infrastructure</p>
        <div class="grid grid-cols-2 mb-5 gap-x-8 gap-y-3 md:grid-cols-4">
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">DB Driver</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst($systemInfo['db_driver']) }}</span>
            </div>
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Database</span>
                <span class="text-sm font-semibold text-gray-900 truncate dark:text-white">{{ $systemInfo['db_database'] }}</span>
            </div>
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Session</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst($systemInfo['session_driver']) }}</span>
            </div>
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Mail</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst($systemInfo['mail_mailer']) }}</span>
            </div>
        </div>

        {{-- Row 4 — Server --}}
        <p class="mb-2 text-xs font-semibold tracking-widest text-gray-400 uppercase dark:text-gray-500">Server</p>
        <div class="grid grid-cols-2 mb-5 gap-x-8 gap-y-3 md:grid-cols-4">
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">OS</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $systemInfo['server_os'] }}</span>
            </div>
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">PHP Memory</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $systemInfo['php_memory_limit'] }}</span>
            </div>
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Extensions</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $systemInfo['php_extensions'] }} loaded</span>
            </div>
            <div class="flex flex-col gap-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <span class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Web Server</span>
                <span class="text-sm font-semibold text-gray-900 truncate dark:text-white">{{ Str::limit($systemInfo['server_software'], 30) }}</span>
            </div>
        </div>

        {{-- Storage / disk bars --}}
        <div class="grid grid-cols-1 gap-4 pt-4 mt-2 border-t border-gray-100 dark:border-gray-700 sm:grid-cols-3">
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
                        {{ $systemInfo['disk_used_pct'] >= 90 ? 'bg-danger-500' : ($systemInfo['disk_used_pct'] >= 70 ? 'bg-warning-500' : 'bg-success-500') }}"
                        style="width: {{ $systemInfo['disk_used_pct'] }}%"></div>
                </div>
            </div>
            @endif
        </div>
    </x-filament::section>

    <div class="grid gap-6 md:grid-cols-2">
        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- Cache Management                                            --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}

        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-s-fire" class="w-5 h-5 text-warning-500" />
                    Cache Management
                </div>
            </x-slot>
            <x-slot name="description">
                Clear cached data or build production caches
            </x-slot>

            {{-- Clear Caches row --}}
            <p class="mb-2 text-xs font-semibold tracking-widest text-gray-400 uppercase dark:text-gray-500">Clear</p>
            <div class="grid grid-cols-2 gap-3 mb-4 sm:grid-cols-4">
                @php
                    $clearButtons = [
                        ['method' => 'clearAppCache',    'label' => 'App Cache',    'cmd' => 'cache:clear',  'color' => 'danger'],
                        ['method' => 'clearConfigCache', 'label' => 'Config Cache', 'cmd' => 'config:clear', 'color' => 'warning'],
                        ['method' => 'clearRouteCache',  'label' => 'Route Cache',  'cmd' => 'route:clear',  'color' => 'success'],
                        ['method' => 'clearViewCache',   'label' => 'View Cache',   'cmd' => 'view:clear',   'color' => 'info'],
                    ];
                @endphp
                @foreach ($clearButtons as $btn)
                    <x-filament::button
                        wire:click="{{ $btn['method'] }}"
                        wire:loading.attr="disabled"
                        :color="$btn['color']"
                        :outlined="true"
                        size="sm"
                        class="flex flex-col items-center gap-1"
                    >
                        <span wire:loading.remove wire:target="{{ $btn['method'] }}">{{ $btn['label'] }}</span>
                        <span wire:loading wire:target="{{ $btn['method'] }}" class="flex items-center gap-1">
                            <x-filament::loading-indicator class="w-3 h-3" />
                            Running
                        </span>
                        <code class="font-mono text-gray-400 dark:text-gray-500" style="font-size:10px">php artisan {{ $btn['cmd'] }}</code>
                    </x-filament::button>
                @endforeach
            </div>

            <x-filament::button
                wire:click="clearAllCaches"
                wire:loading.attr="disabled"
                color="danger"
                class="w-full mb-5"
            >
                <span wire:loading.remove wire:target="clearAllCaches">
                    <x-filament::icon icon="heroicon-s-arrow-path" class="w-4 h-4 shrink-0" />
                </span>
                <span wire:loading wire:target="clearAllCaches">
                    <x-filament::loading-indicator class="w-4 h-4" />
                </span>
                <span wire:loading.remove wire:target="clearAllCaches">Clear All Caches</span>
                <span wire:loading wire:target="clearAllCaches">Clearing...</span>
            </x-filament::button>

            {{-- Build Caches row --}}
            <p class="mb-2 text-xs font-semibold tracking-widest text-gray-400 uppercase dark:text-gray-500">Build</p>
            <div class="grid grid-cols-2 gap-3 mb-4 sm:grid-cols-4">
                @php
                    $buildButtons = [
                        ['method' => 'cacheConfig', 'label' => 'Cache Config', 'cmd' => 'config:cache', 'color' => 'info'],
                        ['method' => 'cacheRoutes', 'label' => 'Cache Routes', 'cmd' => 'route:cache',  'color' => 'success'],
                        ['method' => 'cacheViews',  'label' => 'Cache Views',  'cmd' => 'view:cache',   'color' => 'primary'],
                    ];
                @endphp
                @foreach ($buildButtons as $btn)
                    <x-filament::button
                        wire:click="{{ $btn['method'] }}"
                        wire:loading.attr="disabled"
                        :color="$btn['color']"
                        :outlined="true"
                        size="sm"
                        class="flex flex-col items-center gap-1"
                    >
                        <span wire:loading.remove wire:target="{{ $btn['method'] }}">{{ $btn['label'] }}</span>
                        <span wire:loading wire:target="{{ $btn['method'] }}" class="flex items-center gap-1">
                            <x-filament::loading-indicator class="w-3 h-3" />
                            Running
                        </span>
                        <code class="font-mono text-gray-400 dark:text-gray-500" style="font-size:10px">php artisan {{ $btn['cmd'] }}</code>
                    </x-filament::button>
                @endforeach

                <x-filament::button
                    wire:click="cacheAll"
                    wire:loading.attr="disabled"
                    color="success"
                    size="sm"
                    class="flex flex-col items-center gap-1"
                >
                    <span wire:loading.remove wire:target="cacheAll">Cache All</span>
                    <span wire:loading wire:target="cacheAll" class="flex items-center gap-1">
                        <x-filament::loading-indicator class="w-3 h-3" />
                        Running
                    </span>
                    <code class="font-mono text-gray-400" style="font-size:10px">config+route+view</code>
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Quick Actions --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-s-bolt" class="w-5 h-5 text-purple-500" />
                    Quick Actions
                </div>
            </x-slot>

            <div class="space-y-2">
                <x-filament::button
                    wire:click="optimize"
                    wire:loading.attr="disabled"
                    color="success"
                    class="w-full"
                    :icon="'heroicon-s-cog-6-tooth'"
                >
                    <span wire:loading.remove wire:target="optimize">Optimize Application</span>
                    <span wire:loading wire:target="optimize">Optimizing...</span>
                </x-filament::button>

                <x-filament::button
                    wire:click="storageLink"
                    wire:loading.attr="disabled"
                    color="gray"
                    class="w-full"
                    :icon="'heroicon-s-link'"
                >
                    Create Storage Link
                </x-filament::button>

                <x-filament::button
                    wire:click="toggleMaintenance"
                    wire:loading.attr="disabled"
                    :color="$systemInfo['maintenance_mode'] ? 'success' : 'warning'"
                    class="w-full"
                    :icon="'heroicon-s-wrench-screwdriver'"
                >
                    @if ($systemInfo['maintenance_mode'])
                        Disable Maintenance Mode
                    @else
                        Enable Maintenance Mode
                    @endif
                </x-filament::button>

                <x-filament::button
                    wire:click="viewEnv"
                    wire:loading.attr="disabled"
                    color="gray"
                    class="w-full"
                    :icon="'heroicon-s-code-bracket'"
                >
                    View .env File
                </x-filament::button>

                <x-filament::button
                    wire:click="runSchedule"
                    wire:loading.attr="disabled"
                    color="info"
                    class="w-full"
                    :icon="'heroicon-s-clock'"
                >
                    <span wire:loading.remove wire:target="runSchedule">Run Scheduler</span>
                    <span wire:loading wire:target="runSchedule">Running...</span>
                </x-filament::button>
            </div>
        </x-filament::section>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        {{-- Database Commands --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-s-circle-stack" class="w-5 h-5 text-emerald-500" />
                    Database Commands
                </div>
            </x-slot>

            <div class="space-y-2">
                <x-filament::button
                    wire:click="checkMigrations"
                    wire:loading.attr="disabled"
                    color="gray"
                    class="justify-between w-full"
                >
                    <span wire:loading.remove wire:target="checkMigrations">Migration Status</span>
                    <span wire:loading wire:target="checkMigrations">Checking...</span>
                    <x-filament::icon icon="heroicon-s-chevron-right" class="w-4 h-4 shrink-0" slot="after" />
                </x-filament::button>

                <x-filament::button
                    wire:click="runMigrations"
                    wire:loading.attr="disabled"
                    color="success"
                    class="justify-between w-full"
                    :icon="'heroicon-s-play'"
                >
                    <span wire:loading.remove wire:target="runMigrations">Run Migrations</span>
                    <span wire:loading wire:target="runMigrations">Migrating...</span>
                    <x-filament::icon icon="heroicon-s-chevron-right" class="w-4 h-4 shrink-0" slot="after" />
                </x-filament::button>

                <x-filament::button
                    x-on:click="
                        if (confirm('⚠️ WARNING: This will DROP ALL TABLES and reseed. This cannot be undone!\n\nContinue?')) {
                            $wire.call('migrateFreshSeed');
                        }
                    "
                    color="danger"
                    class="justify-between w-full"
                    :icon="'heroicon-s-exclamation-triangle'"
                >
                    Fresh Migration + Seed
                    <span class="text-xs opacity-70">(drops all tables)</span>
                    <x-filament::icon icon="heroicon-s-chevron-right" class="w-4 h-4 shrink-0" slot="after" />
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Queue Management --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-s-queue-list" class="w-5 h-5 text-violet-500" />
                    Queue Management
                </div>
            </x-slot>

            <div class="space-y-2">
                <x-filament::button
                    wire:click="getQueueStats"
                    wire:loading.attr="disabled"
                    color="gray"
                    class="w-full"
                    :icon="'heroicon-s-chart-bar'"
                >
                    <span wire:loading.remove wire:target="getQueueStats">Statistics</span>
                    <span wire:loading wire:target="getQueueStats">Loading...</span>
                </x-filament::button>

                <x-filament::button
                    wire:click="restartQueue"
                    wire:loading.attr="disabled"
                    color="info"
                    class="w-full"
                    :icon="'heroicon-s-arrow-path'"
                >
                    <span wire:loading.remove wire:target="restartQueue">Restart Workers</span>
                    <span wire:loading wire:target="restartQueue">Sending...</span>
                </x-filament::button>

                <x-filament::button
                    wire:click="checkQueueStatus"
                    wire:loading.attr="disabled"
                    color="success"
                    class="w-full"
                    :icon="'heroicon-s-check-circle'"
                >
                    <span wire:loading.remove wire:target="checkQueueStatus">Check Status</span>
                    <span wire:loading wire:target="checkQueueStatus">Checking...</span>
                </x-filament::button>

                <x-filament::button
                    wire:click="retryFailedJobs"
                    wire:loading.attr="disabled"
                    color="warning"
                    class="w-full"
                    :icon="'heroicon-s-arrow-uturn-left'"
                >
                    <span wire:loading.remove wire:target="retryFailedJobs">Retry Failed</span>
                    <span wire:loading wire:target="retryFailedJobs">Retrying...</span>
                </x-filament::button>

                <x-filament::button
                    x-on:click="
                        if (confirm('Permanently delete ALL failed jobs? This cannot be undone.')) {
                            $wire.call('flushFailedJobs');
                        }
                    "
                    color="danger"
                    class="w-full"
                    :icon="'heroicon-s-trash'"
                >
                    Flush Failed Jobs
                </x-filament::button>

                <x-filament::button
                    wire:click="startDaemonQueue"
                    color="secondary"
                    class="w-full"
                    :icon="'heroicon-s-bolt'"
                >
                    Daemon Info
                </x-filament::button>
            </div>
        </x-filament::section>
    </div>

    {{-- Session Management --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <x-filament::icon icon="heroicon-s-key" class="w-5 h-5 text-teal-500" />
                Session Management
            </div>
        </x-slot>
        <x-slot name="description">
            Guest session lifecycle control
        </x-slot>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <x-filament::button
                wire:click="showGuestSessionStats"
                wire:loading.attr="disabled"
                color="gray"
                class="w-full"
                :icon="'heroicon-s-chart-bar'"
            >
                <span wire:loading.remove wire:target="showGuestSessionStats">Session Statistics</span>
                <span wire:loading wire:target="showGuestSessionStats">Loading...</span>
            </x-filament::button>

            <x-filament::button
                x-on:click="
                    if (confirm('Mark expired sessions and delete old ones older than 1 day. Continue?')) {
                        $wire.call('clearExpiredSessions');
                    }
                "
                color="danger"
                class="w-full"
                :icon="'heroicon-s-trash'"
            >
                Clear Expired Sessions
            </x-filament::button>
        </div>
    </x-filament::section>

    {{-- Log Viewer --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <x-filament::icon icon="heroicon-s-document-text" class="w-5 h-5 text-rose-500" />
                Log Viewer
            </div>
        </x-slot>
        <x-slot name="description">
            laravel.log — <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $logSize }} MB</span>
        </x-slot>

        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <div class="flex flex-wrap items-center gap-2">
                @foreach ([25, 50, 100, 200, 500, 1000] as $preset)
                    <x-filament::button
                        wire:click="$set('logLines', {{ $preset }})"
                        :color="(int)$logLines === $preset ? 'primary' : 'gray'"
                        size="xs"
                        :outlined="(int)$logLines !== $preset"
                    >
                        {{ $preset }}
                    </x-filament::button>
                @endforeach
            </div>

            <div class="flex items-center gap-2">
                <x-filament::button
                    wire:click="viewLogs"
                    wire:loading.attr="disabled"
                    color="danger"
                    :icon="'heroicon-s-eye'"
                >
                    <span wire:loading.remove wire:target="viewLogs">View</span>
                    <span wire:loading wire:target="viewLogs">
                        <x-filament::loading-indicator class="w-4 h-4" />
                        Loading…
                    </span>
                </x-filament::button>

                <x-filament::button
                    x-on:click="
                        if (confirm('Clear laravel.log? This cannot be undone.')) {
                            $wire.call('clearLogs');
                        }
                    "
                    color="gray"
                    :icon="'heroicon-s-trash'"
                >
                    Clear
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>

    {{-- Command Output --}}
    <div
        x-data="{
            copied: false,
            copyOutput() {
                const text = document.getElementById('terminal-output').innerText;
                navigator.clipboard.writeText(text).then(() => {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                });
            }
        }"
        class="overflow-hidden border border-gray-800 shadow-2xl rounded-xl bg-gray-950"
    >
        {{-- Title bar --}}
        <div class="flex items-center justify-between px-4 py-2.5 bg-gray-900 border-b border-gray-800">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 transition-colors bg-red-500 rounded-full cursor-default hover:bg-red-400"></span>
                <span class="w-3 h-3 transition-colors bg-yellow-400 rounded-full cursor-default hover:bg-yellow-300"></span>
                <span class="w-3 h-3 transition-colors bg-green-500 rounded-full cursor-default hover:bg-green-400"></span>
            </div>

            <div class="flex items-center gap-1.5 font-mono text-xs text-gray-500 select-none">
                <x-filament::icon icon="heroicon-o-command-line" class="w-3.5 h-3.5 shrink-0 text-gray-600" />
                <span class="text-gray-600">~/</span><span class="text-emerald-400">laravel</span>
                <span class="text-gray-600 mx-0.5">›</span>
                <span class="text-sky-400">php artisan</span>
            </div>

            <div class="flex items-center gap-2">
                <button @click="copyOutput()" :disabled="!$wire.output"
                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-md transition-all
                        text-gray-400 hover:text-white hover:bg-gray-700 disabled:opacity-30 disabled:cursor-not-allowed">
                    <template x-if="!copied">
                        <x-filament::icon icon="heroicon-s-clipboard" class="w-3.5 h-3.5" />
                    </template>
                    <template x-if="copied">
                        <x-filament::icon icon="heroicon-s-check" class="w-3.5 h-3.5 text-emerald-400" />
                    </template>
                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                </button>

                <div class="w-px h-4 bg-gray-700"></div>

                <button wire:click="$set('output', '')" :disabled="!$wire.output"
                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-md transition-all
                        text-gray-400 hover:text-red-400 hover:bg-gray-700 disabled:opacity-30 disabled:cursor-not-allowed">
                    <x-filament::icon icon="heroicon-s-trash" class="w-3.5 h-3.5" />
                    Clear
                </button>
            </div>
        </div>

        {{-- Gutter + output --}}
        <div class="flex">
            <div class="hidden sm:flex flex-col items-end pt-5 pb-5 pl-3 pr-3 bg-gray-900/50 border-r border-gray-800 select-none min-w-[2.75rem]"
                aria-hidden="true">
                @if ($output)
                    @php $lineCount = substr_count($output, "\n") + 1; @endphp
                    @for ($i = 1; $i <= min($lineCount, 200); $i++)
                        <span class="font-mono leading-relaxed text-gray-700" style="font-size:11px">{{ $i }}</span>
                    @endfor
                @else
                    <span class="font-mono leading-relaxed text-gray-700" style="font-size:11px">1</span>
                @endif
            </div>

            <div class="flex-1 overflow-x-auto overflow-y-auto max-h-[28rem] p-5" id="terminal-output"
                wire:key="terminal-output-{{ strlen($output) }}">
                @if ($output)
                    @foreach (explode("\n", $output) as $line)
                        @php
                            $trimmed = ltrim($line);
                            $cls = match(true) {
                                str_starts_with($trimmed, '✅') || str_starts_with($trimmed, '🎉') || str_starts_with($trimmed, '🔗') => 'text-emerald-400',
                                str_starts_with($trimmed, '❌') => 'text-red-400',
                                str_starts_with($trimmed, '⚠️') || str_starts_with($trimmed, '⚙️') || str_starts_with($trimmed, '🔧') || str_starts_with($trimmed, '🚀') => 'text-amber-400',
                                str_starts_with($trimmed, 'Running:') => 'text-sky-400',
                                str_starts_with($trimmed, 'php artisan') || str_starts_with($trimmed, '  php artisan') => 'text-violet-400',
                                str_starts_with($trimmed, '#') => 'text-gray-600',
                                default => 'text-gray-300'
                            };
                        @endphp
                        <div class="font-mono leading-relaxed {{ $cls }}" style="font-size:13px">{{ $line ?: '&nbsp;' }}</div>
                    @endforeach
                @else
                    <div class="flex flex-col items-center justify-center h-40 gap-3 select-none">
                        <x-filament::icon icon="heroicon-o-command-line" class="w-10 h-10 text-gray-700" />
                        <p class="font-mono text-sm text-gray-600">Run a command above — output will appear here.</p>
                    </div>
                @endif
            </div>
        </div>

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

    {{-- Command History --}}
    @if (count($commandHistory) > 0)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <x-filament::icon icon="heroicon-s-clock" class="w-5 h-5 text-indigo-500" />
                        Command History
                        <span class="ml-1 text-sm font-normal text-gray-400">(last {{ count($commandHistory) }})</span>
                    </div>
                    <x-filament::button wire:click="clearHistory" color="gray" size="xs" :icon="'heroicon-s-trash'">
                        Clear History
                    </x-filament::button>
                </div>
            </x-slot>

           <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <!-- your table content -->
    </table>
</div>
        </x-filament::section>
    @endif

</div>


