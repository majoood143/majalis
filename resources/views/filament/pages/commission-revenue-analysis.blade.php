<div class="p-6">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Bookings -->
        <div class="rounded-lg border bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $stats['total_bookings'] ?? 0 }}
                    </p>
                </div>
                <div class="rounded-full bg-blue-100 p-3">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="rounded-lg border bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">
                        {{ number_format($stats['total_revenue'] ?? 0, 3) }} OMR
                    </p>
                </div>
                <div class="rounded-full bg-green-100 p-3">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Commission Earned -->
        <div class="rounded-lg border bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Commission Earned</p>
                    <p class="mt-2 text-3xl font-bold text-orange-600">
                        {{ number_format($stats['total_commission'] ?? 0, 3) }} OMR
                    </p>
                </div>
                <div class="rounded-full bg-orange-100 p-3">
                    <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Commission -->
        <div class="rounded-lg border bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Avg. Commission</p>
                    <p class="mt-2 text-3xl font-bold text-purple-600">
                        {{ number_format($stats['average_commission'] ?? 0, 3) }} OMR
                    </p>
                </div>
                <div class="rounded-full bg-purple-100 p-3">
                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Setting Details -->
    <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Commission Setting Details</h3>
        <dl class="grid gap-4 md:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-600">Commission Name</dt>
                <dd class="mt-1 text-base text-gray-900">{{ $commission->name ?? 'Default Commission' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Commission Type</dt>
                <dd class="mt-1 text-base text-gray-900">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $commission->commission_type?->value ? ucfirst($commission->commission_type->value) : 'N/A' }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Commission Value</dt>
                <dd class="mt-1 text-base font-semibold text-orange-600">
                    @if($commission->commission_type?->value === 'percentage')
                        {{ number_format($commission->commission_value, 2) }}%
                    @else
                        {{ number_format($commission->commission_value, 3) }} OMR
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Scope</dt>
                <dd class="mt-1 text-base text-gray-900">
                    @if($commission->hall_id)
                        Hall-Specific
                    @elseif($commission->owner_id)
                        Owner-Specific
                    @else
                        Global
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Effective Period</dt>
                <dd class="mt-1 text-base text-gray-900">
                    {{ $commission->effective_from ? $commission->effective_from->format('M d, Y') : 'No start date' }} 
                    - 
                    {{ $commission->effective_to ? $commission->effective_to->format('M d, Y') : 'Ongoing' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Status</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $commission->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $commission->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    <!-- Revenue Breakdown -->
    <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Revenue Impact</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Total Booking Revenue</span>
                <span class="font-semibold text-gray-900">{{ number_format($stats['total_revenue'] ?? 0, 3) }} OMR</span>
            </div>
            <div class="flex items-center justify-between border-t pt-3">
                <span class="text-sm text-gray-600">Platform Commission</span>
                <span class="font-semibold text-orange-600">{{ number_format($stats['total_commission'] ?? 0, 3) }} OMR</span>
            </div>
            <div class="flex items-center justify-between border-t pt-3">
                <span class="text-sm font-medium text-gray-900">Owner Payout</span>
                <span class="text-lg font-bold text-green-600">{{ number_format(($stats['total_revenue'] ?? 0) - ($stats['total_commission'] ?? 0), 3) }} OMR</span>
            </div>
        </div>
    </div>

    <!-- Note -->
    <div class="mt-4 rounded-lg bg-blue-50 p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm text-blue-700">
                Revenue statistics are calculated based on confirmed and completed bookings with paid status during the commission's effective period.
            </p>
        </div>
    </div>
</div>
