<div class="fi-user-menu-language-switcher">
    @livewire(\App\Livewire\LanguageSwitcher::class)
</div>

{{-- Add RTL support styles --}}
@if(app()->getLocale() === 'ar')
    <style>
        /* RTL Layout Adjustments */
        :root {
            direction: rtl;
        }

        .fi-sidebar {
            left: auto;
            right: 0;
        }

        .fi-sidebar-nav {
            direction: rtl;
        }

        /* Adjust icons and spacing for RTL */
        .fi-sidebar-item-icon {
            margin-left: 0.75rem;
            margin-right: 0;
        }

        /* Form fields RTL adjustments */
        .fi-fo-field-wrp-label {
            text-align: right;
        }

        /* Table RTL adjustments */
        .fi-ta-table {
            direction: rtl;
        }

        /* Notification RTL adjustments */
        .fi-no-notification {
            direction: rtl;
            text-align: right;
        }
    </style>
@endif
