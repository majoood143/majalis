@php
    $icon = $record->icon ?? 'heroicon-o-check-badge';
    // Ensure icon has heroicon prefix
    if (!str_starts_with($icon, 'heroicon-')) {
        $icon = 'heroicon-o-' . $icon;
    }
@endphp

<div class="flex items-center justify-center w-16 h-16 rounded-xl bg-primary-100 dark:bg-primary-900/30">
    <x-dynamic-component 
        :component="$icon" 
        class="w-8 h-8 text-primary-600 dark:text-primary-400" 
    />
</div>
