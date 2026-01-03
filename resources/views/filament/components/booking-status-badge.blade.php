{{-- 
    Booking Status Badge Component
    Displays booking status and payment status as badges
    
    @param string $status - The booking status (pending, confirmed, completed, cancelled)
    @param string $paymentStatus - The payment status (pending, partial, paid, failed, refunded)
--}}

<div class="flex flex-wrap gap-2">
    {{-- Booking Status Badge --}}
    @php
        $statusConfig = match($status) {
            'pending' => [
                'color' => 'warning',
                'icon' => 'heroicon-m-clock',
                'label' => __('Pending'),
            ],
            'confirmed' => [
                'color' => 'success',
                'icon' => 'heroicon-m-check-circle',
                'label' => __('Confirmed'),
            ],
            'completed' => [
                'color' => 'info',
                'icon' => 'heroicon-m-check-badge',
                'label' => __('Completed'),
            ],
            'cancelled' => [
                'color' => 'danger',
                'icon' => 'heroicon-m-x-circle',
                'label' => __('Cancelled'),
            ],
            default => [
                'color' => 'gray',
                'icon' => 'heroicon-m-question-mark-circle',
                'label' => ucfirst($status ?? 'Unknown'),
            ],
        };
    @endphp
    
    <x-filament::badge 
        :color="$statusConfig['color']"
        :icon="$statusConfig['icon']"
        size="lg"
    >
        {{ $statusConfig['label'] }}
    </x-filament::badge>

    {{-- Payment Status Badge --}}
    @php
        $paymentConfig = match($paymentStatus) {
            'pending' => [
                'color' => 'warning',
                'icon' => 'heroicon-m-clock',
                'label' => __('Payment Pending'),
            ],
            'partial' => [
                'color' => 'info',
                'icon' => 'heroicon-m-arrow-path',
                'label' => __('Partial Payment'),
            ],
            'paid' => [
                'color' => 'success',
                'icon' => 'heroicon-m-check-circle',
                'label' => __('Paid'),
            ],
            'failed' => [
                'color' => 'danger',
                'icon' => 'heroicon-m-x-circle',
                'label' => __('Payment Failed'),
            ],
            'refunded' => [
                'color' => 'gray',
                'icon' => 'heroicon-m-arrow-uturn-left',
                'label' => __('Refunded'),
            ],
            default => [
                'color' => 'gray',
                'icon' => 'heroicon-m-question-mark-circle',
                'label' => ucfirst($paymentStatus ?? 'Unknown'),
            ],
        };
    @endphp
    
    <x-filament::badge 
        :color="$paymentConfig['color']"
        :icon="$paymentConfig['icon']"
        size="lg"
    >
        {{ $paymentConfig['label'] }}
    </x-filament::badge>
</div>
