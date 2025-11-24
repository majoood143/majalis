{{--
    Gateway Response Modal View

    Displays Thawani payment gateway response data in a formatted modal.
    Used in Filament Payment resource to show detailed API response.

    @var array|null $data - The gateway response data from payment record
--}}

<div class="space-y-4">
    @if($data && is_array($data))
        {{-- Success Status Badge --}}
        @if(isset($data['success']))
            <div class="flex items-center gap-2 mb-4">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Status:
                </span>
                @if($data['success'] === true)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Success
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Failed
                    </span>
                @endif
            </div>
        @endif

        {{-- Key Information Cards --}}
        @if(isset($data['data']))
            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                {{-- Session ID --}}
                @if(isset($data['data']['session_id']))
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <dt class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                            Session ID
                        </dt>
                        <dd class="font-mono text-sm text-gray-900 break-all dark:text-gray-100">
                            {{ $data['data']['session_id'] }}
                        </dd>
                    </div>
                @endif

                {{-- Invoice Number --}}
                @if(isset($data['data']['invoice']))
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <dt class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                            Invoice Number
                        </dt>
                        <dd class="font-mono text-sm text-gray-900 dark:text-gray-100">
                            {{ $data['data']['invoice'] }}
                        </dd>
                    </div>
                @endif

                {{-- Payment Status --}}
                @if(isset($data['data']['payment_status']))
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <dt class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                            Payment Status
                        </dt>
                        <dd class="text-sm font-semibold text-gray-900 capitalize dark:text-gray-100">
                            {{ $data['data']['payment_status'] }}
                        </dd>
                    </div>
                @endif

                {{-- Total Amount --}}
                @if(isset($data['data']['total_amount']))
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <dt class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                            Total Amount
                        </dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ number_format($data['data']['total_amount'] / 1000, 3) }}
                            <span class="text-gray-500">{{ $data['data']['currency'] ?? 'OMR' }}</span>
                        </dd>
                    </div>
                @endif

                {{-- Created At --}}
                @if(isset($data['data']['created_at']))
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <dt class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                            Created At
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                            {{ \Carbon\Carbon::parse($data['data']['created_at'])->format('Y-m-d H:i:s') }}
                        </dd>
                    </div>
                @endif

                {{-- Expires At --}}
                @if(isset($data['data']['expire_at']))
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <dt class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                            Expires At
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                            {{ \Carbon\Carbon::parse($data['data']['expire_at'])->format('Y-m-d H:i:s') }}
                        </dd>
                    </div>
                @endif
            </div>

            {{-- Products Information --}}
            @if(isset($data['data']['products']) && is_array($data['data']['products']))
                <div class="p-4 mb-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                    <h4 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Products
                    </h4>
                    <div class="space-y-2">
                        @foreach($data['data']['products'] as $product)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-900 dark:text-gray-100">
                                    {{ $product['name'] ?? 'N/A' }}
                                    <span class="text-gray-500">× {{ $product['quantity'] ?? 1 }}</span>
                                </span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ number_format(($product['unit_amount'] ?? 0) / 1000, 3) }} OMR
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        {{-- Response Code & Description --}}
        @if(isset($data['code']) || isset($data['description']))
            <div class="p-4 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800">
                @if(isset($data['code']))
                    <div class="mb-2">
                        <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Code:</span>
                        <span class="ml-2 text-sm text-blue-900 dark:text-blue-100">{{ $data['code'] }}</span>
                    </div>
                @endif
                @if(isset($data['description']))
                    <div>
                        <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Description:</span>
                        <span class="ml-2 text-sm text-blue-900 dark:text-blue-100">{{ $data['description'] }}</span>
                    </div>
                @endif
            </div>
        @endif

        {{-- Raw JSON Response (Collapsible) --}}
        <details class="mt-4">
            <summary class="text-sm font-medium text-gray-700 cursor-pointer select-none dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                <span class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    View Raw JSON Response
                </span>
            </summary>
            <div class="p-4 mt-3 overflow-x-auto bg-gray-900 rounded-lg">
                <pre class="font-mono text-xs text-green-400">{{ json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </details>
    @else
        {{-- No Data Available --}}
        <div class="flex flex-col items-center justify-center py-8 text-gray-500 dark:text-gray-400">
            <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm font-medium">No gateway response data available</p>
            <p class="mt-1 text-xs">Payment may not have been processed through the gateway</p>
        </div>
    @endif
</div>

<style>
    /* Smooth details animation */
    details > summary {
        list-style: none;
    }

    details > summary::-webkit-details-marker {
        display: none;
    }

    details[open] > summary svg {
        transform: rotate(90deg);
        transition: transform 0.2s ease-in-out;
    }

    details > summary svg {
        transition: transform 0.2s ease-in-out;
    }
</style>
```


