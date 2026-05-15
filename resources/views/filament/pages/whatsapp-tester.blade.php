<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="mb-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        WhatsApp Message Tester
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Test all WhatsApp message types supported by the plugin. Make sure you have a connected instance and Evolution API v2.4+ for interactive messages.
                    </p>
                </div>

                {{ $this->form }}

                <div class="mt-6">
                    @foreach($this->getFormActions() as $action)
                        {{ $action }}
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Testing Tips</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Ensure your Evolution API instance is running and accessible via EVOLUTION_URL</li>
                            <li>Create and connect a WhatsApp instance first via WhatsApp > Instances</li>
                            <li>Interactive messages (Buttons, List, Carousel) require Evolution API v2.4+</li>
                            <li>Phone numbers must be in international format: 5511999999999 (no '+' or spaces)</li>
                            <li>For media files, ensure your storage disk is properly configured</li>
                            <li>Make sure the queue worker is running: <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">php artisan queue:work</code></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
