<x-filament-panels::page>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-500">
                <x-heroicon-o-cog class="h-6 w-6 text-white" />
            </div>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                    System Maintenance
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Clear caches and optimize your Laravel Filament application
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Introduction -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Maintenance Commands</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Use the buttons below to execute common Laravel Artisan commands for clearing various caches.
                    These commands help resolve issues with cached configuration, routes, or views in your Filament project.
                </p>
            </div>
            
            @if(app()->environment('production'))
            <div class="rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                <div class="flex">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-red-400 dark:text-red-300" />
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Production Environment</h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <p>You are running in production mode. Use these commands with caution as they may affect live users.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Commands Grid -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Cache Clear -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/30">
                            <x-heroicon-o-trash class="h-5 w-5 text-red-600 dark:text-red-400" />
                        </div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Application Cache</h4>
                    </div>
                </div>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    Removes the cached files used to speed up your application. Use when you're not seeing data updates.
                </p>
                <div class="space-y-4">
                    <div class="rounded-lg bg-gray-900 p-3 font-mono text-sm text-white dark:bg-gray-900">
                        <code>php artisan cache:clear</code>
                    </div>
                    <div class="flex gap-3">
                        <button 
                            type="button"
                            x-data="{
                                copyCommand() {
                                    navigator.clipboard.writeText('php artisan cache:clear');
                                    $dispatch('notify', { title: 'Copied!', message: 'Command copied to clipboard', status: 'success' });
                                }
                            }"
                            @click="copyCommand"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            <x-heroicon-o-clipboard class="h-4 w-4" />
                            Copy
                        </button>
                        <button 
                            type="button"
                            wire:click="runCommand('cache:clear')"
                            wire:loading.attr="disabled"
                            wire:target="runCommand"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="runCommand">
                                <x-heroicon-o-play class="h-4 w-4" />
                            </span>
                            <span wire:loading wire:target="runCommand">
                                <x-filament::loading-indicator class="h-4 w-4" />
                            </span>
                            <span wire:loading.remove wire:target="runCommand">Run Command</span>
                            <span wire:loading wire:target="runCommand">Running...</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Config Clear -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30">
                            <x-heroicon-o-cog-6-tooth class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                        </div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Config Cache</h4>
                    </div>
                </div>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    Removes the cached configuration files. Use after making changes to your config files.
                </p>
                <div class="space-y-4">
                    <div class="rounded-lg bg-gray-900 p-3 font-mono text-sm text-white dark:bg-gray-900">
                        <code>php artisan config:clear</code>
                    </div>
                    <div class="flex gap-3">
                        <button 
                            type="button"
                            x-data="{
                                copyCommand() {
                                    navigator.clipboard.writeText('php artisan config:clear');
                                    $dispatch('notify', { title: 'Copied!', message: 'Command copied to clipboard', status: 'success' });
                                }
                            }"
                            @click="copyCommand"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            <x-heroicon-o-clipboard class="h-4 w-4" />
                            Copy
                        </button>
                        <button 
                            type="button"
                            wire:click="runCommand('config:clear')"
                            wire:loading.attr="disabled"
                            wire:target="runCommand"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="runCommand">
                                <x-heroicon-o-play class="h-4 w-4" />
                            </span>
                            <span wire:loading wire:target="runCommand">
                                <x-filament::loading-indicator class="h-4 w-4" />
                            </span>
                            <span wire:loading.remove wire:target="runCommand">Run Command</span>
                            <span wire:loading wire:target="runCommand">Running...</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Route Clear -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                            <x-heroicon-o-map class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Route Cache</h4>
                    </div>
                </div>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    Clears the route cache file. Use after adding or modifying routes in your application.
                </p>
                <div class="space-y-4">
                    <div class="rounded-lg bg-gray-900 p-3 font-mono text-sm text-white dark:bg-gray-900">
                        <code>php artisan route:clear</code>
                    </div>
                    <div class="flex gap-3">
                        <button 
                            type="button"
                            x-data="{
                                copyCommand() {
                                    navigator.clipboard.writeText('php artisan route:clear');
                                    $dispatch('notify', { title: 'Copied!', message: 'Command copied to clipboard', status: 'success' });
                                }
                            }"
                            @click="copyCommand"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            <x-heroicon-o-clipboard class="h-4 w-4" />
                            Copy
                        </button>
                        <button 
                            type="button"
                            wire:click="runCommand('route:clear')"
                            wire:loading.attr="disabled"
                            wire:target="runCommand"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="runCommand">
                                <x-heroicon-o-play class="h-4 w-4" />
                            </span>
                            <span wire:loading wire:target="runCommand">
                                <x-filament::loading-indicator class="h-4 w-4" />
                            </span>
                            <span wire:loading.remove wire:target="runCommand">Run Command</span>
                            <span wire:loading wire:target="runCommand">Running...</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Clear -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                            <x-heroicon-o-eye class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">View Cache</h4>
                    </div>
                </div>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    Removes all compiled view files. Use when changes to Blade templates are not reflecting.
                </p>
                <div class="space-y-4">
                    <div class="rounded-lg bg-gray-900 p-3 font-mono text-sm text-white dark:bg-gray-900">
                        <code>php artisan view:clear</code>
                    </div>
                    <div class="flex gap-3">
                        <button 
                            type="button"
                            x-data="{
                                copyCommand() {
                                    navigator.clipboard.writeText('php artisan view:clear');
                                    $dispatch('notify', { title: 'Copied!', message: 'Command copied to clipboard', status: 'success' });
                                }
                            }"
                            @click="copyCommand"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            <x-heroicon-o-clipboard class="h-4 w-4" />
                            Copy
                        </button>
                        <button 
                            type="button"
                            wire:click="runCommand('view:clear')"
                            wire:loading.attr="disabled"
                            wire:target="runCommand"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="runCommand">
                                <x-heroicon-o-play class="h-4 w-4" />
                            </span>
                            <span wire:loading wire:target="runCommand">
                                <x-filament::loading-indicator class="h-4 w-4" />
                            </span>
                            <span wire:loading.remove wire:target="runCommand">Run Command</span>
                            <span wire:loading wire:target="runCommand">Running...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Section -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                    <x-heroicon-o-bolt class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                </div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Advanced Options</h4>
            </div>
            <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                Run all cache clearing commands at once. This is useful when you want to ensure a completely fresh state for your application.
            </p>
            <button 
                type="button"
                wire:click="runAllCommands"
                wire:loading.attr="disabled"
                wire:target="runAllCommands"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 px-5 py-3 text-sm font-medium text-white shadow-sm transition hover:from-purple-500 hover:to-pink-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="runAllCommands">
                    <x-heroicon-o-arrow-path class="h-5 w-5" />
                </span>
                <span wire:loading wire:target="runAllCommands">
                    <x-filament::loading-indicator class="h-5 w-5" />
                </span>
                <span wire:loading.remove wire:target="runAllCommands">Clear All Caches</span>
                <span wire:loading wire:target="runAllCommands">Clearing All Caches...</span>
            </button>
        </div>

        <!-- Output Section -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                        <x-heroicon-o-command-line class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Command Output</h4>
                </div>
                <button 
                    type="button"
                    wire:click="$set('output', '')"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    <x-heroicon-o-trash class="h-4 w-4" />
                    Clear Output
                </button>
            </div>
            <div class="rounded-lg bg-gray-900 p-4 font-mono text-sm text-white dark:bg-gray-900">
                <pre class="whitespace-pre-wrap" x-text="$wire.output || 'Output from executed commands will appear here...'"></pre>
            </div>
        </div>

        <!-- Important Information -->
        <div class="rounded-xl border border-blue-200 bg-blue-50 p-6 dark:border-blue-800 dark:bg-blue-900/20">
            <div class="flex">
                <x-heroicon-o-information-circle class="h-5 w-5 text-blue-400 dark:text-blue-300" />
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Important Information</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p class="mb-2">This page executes actual Artisan commands on your server. Ensure you understand what each command does before running it.</p>
                        <p class="mb-2">For security, this page is only accessible to administrators. In production, consider:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Running commands during low-traffic periods</li>
                            <li>Having a backup before clearing caches</li>
                            <li>Monitoring your application after running commands</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        // Add any Alpine.js functionality here if needed
    </script>
    @endscript
</x-filament-panels::page>