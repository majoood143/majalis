<div class="space-y-3">
    @forelse($activities ?? [] as $activity)
        <div class="py-3 first:pt-0 last:pb-0 border-b border-gray-200 dark:border-gray-700 last:border-0">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                        <x-heroicon-o-clock class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                    </div>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $activity->description }}
                        </p>
                        <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            {{ $activity->created_at->diffForHumans() }}
                        </span>
                    </div>

                    @if($activity->causer)
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                            by {{ $activity->causer->name ?? $activity->causer->email }}
                        </p>
                    @endif

                    @if($activity->properties && count($activity->properties) > 0)
                        <div class="mt-2">
                            <details class="text-xs">
                                <summary class="cursor-pointer text-primary-600 dark:text-primary-400 hover:underline">
                                    View details
                                </summary>
                                <div class="mt-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
                                    <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </details>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-6">
            <x-heroicon-o-clock class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" />
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                No activity recorded yet
            </p>
        </div>
    @endforelse
</div>