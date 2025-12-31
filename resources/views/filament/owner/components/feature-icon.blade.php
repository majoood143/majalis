@php
    $icon = $getRecord()->icon ?? 'heroicon-o-check-badge';
    // Ensure icon has heroicon prefix
    if (!str_starts_with($icon, 'heroicon-')) {
        $icon = 'heroicon-o-' . $icon;
    }
@endphp

<div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30">
    <x-dynamic-component 
        :component="$icon" 
        class="w-5 h-5 text-primary-600 dark:text-primary-400" 
    />
</div>
