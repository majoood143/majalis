<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\BookingResource\Pages;

use App\Filament\Owner\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Hall;
use App\Services\PdfExportService;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * ListBookings Page for Owner Panel
 *
 * Displays all bookings for the authenticated owner's halls
 * with tabs for quick filtering by status.
 */
class ListBookings extends ListRecords
{
    /**
     * The resource class this page belongs to.
     */
    protected static string $resource = BookingResource::class;

    /**
     * Get the header actions for this page.
     */
    protected function getHeaderActions(): array
    {
        return [
            // Owners cannot create bookings - removed CreateAction
            // Actions\Action::make('export')
            //     ->label(__('owner_booking.pages.list.export_label'))
            //     ->icon('heroicon-o-arrow-down-tray')
            //     ->color('gray')
            //     ->action(function () {
            //         // TODO: Implement export
            //         $this->notify('info', __('owner_booking.pages.list.export_notification'));
            //     }),

            Actions\Action::make('export')
                ->label(__('owner_booking.pages.list.export_label'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->modalHeading(__('owner_booking.export.modal_heading'))
                ->modalDescription(__('owner_booking.export.modal_description'))
                ->modalSubmitActionLabel(__('owner_booking.export.submit_label'))
                ->form([
                    Forms\Components\Select::make('hall_id')
                        ->label(__('owner_booking.export.hall_label'))
                        ->options(fn () => Hall::where('owner_id', Auth::id())
                            ->get()
                            ->mapWithKeys(fn ($h) => [
                                $h->id => ($h->name[app()->getLocale()] ?? $h->name['en'] ?? $h->name['ar'] ?? __('owner_booking.general.na')),
                            ]))
                        ->placeholder(__('owner_booking.export.all_halls'))
                        ->nullable(),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\DatePicker::make('date_from')
                                ->label(__('owner_booking.export.date_from'))
                                ->nullable(),
                            Forms\Components\DatePicker::make('date_to')
                                ->label(__('owner_booking.export.date_to'))
                                ->nullable(),
                        ]),
                ])
                ->action(function (array $data): \Symfony\Component\HttpFoundation\StreamedResponse {
                    $query = Booking::query()
                        ->whereHas('hall', fn (Builder $q) => $q->where('owner_id', Auth::id()))
                        ->with(['hall', 'user', 'extraServices'])
                        ->orderBy('booking_date', 'desc');

                    if (!empty($data['hall_id'])) {
                        $query->where('hall_id', $data['hall_id']);
                    }

                    if (!empty($data['date_from'])) {
                        $query->whereDate('booking_date', '>=', $data['date_from']);
                    }

                    if (!empty($data['date_to'])) {
                        $query->whereDate('booking_date', '<=', $data['date_to']);
                    }

                    $bookings = $query->get();
                    $hall     = !empty($data['hall_id']) ? Hall::find($data['hall_id']) : null;

                    $stats = [
                        'total'          => $bookings->count(),
                        'confirmed'      => $bookings->where('status', 'confirmed')->count(),
                        'completed'      => $bookings->where('status', 'completed')->count(),
                        'pending'        => $bookings->where('status', 'pending')->count(),
                        'cancelled'      => $bookings->where('status', 'cancelled')->count(),
                        'total_earnings' => $bookings->whereIn('status', ['confirmed', 'completed'])->sum('owner_payout'),
                    ];

                    $filename = 'bookings-' . now()->format('Y-m-d') . '.pdf';

                    return (new PdfExportService(['direction' => app()->getLocale() === 'ar' ? 'rtl' : 'ltr']))
                        ->generateFromView('pdf.owner-bookings-export', [
                            'bookings'    => $bookings,
                            'hall'        => $hall,
                            'ownerName'   => Auth::user()->name,
                            'dateFrom'    => $data['date_from'] ?? null,
                            'dateTo'      => $data['date_to'] ?? null,
                            'stats'       => $stats,
                            'locale'      => app()->getLocale(),
                            'generatedAt' => now(),
                        ])
                        ->download($filename);
                }),
        ];
    }

    /**
     * Get the tabs for filtering bookings by status.
     */
    public function getTabs(): array
    {
        //$baseQuery = fn() => $this->getFilteredTableQuery();
        $baseQuery = fn() => static::getResource()::getEloquentQuery();

        return [
            'all' => Tab::make(__('owner_booking.pages.list.tabs.all'))
                ->icon('heroicon-o-rectangle-stack')
                ->badge(fn() => $baseQuery()->count())
                ->badgeColor('gray'),

            'pending' => Tab::make(__('owner_booking.pages.list.tabs.pending'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(fn() => $baseQuery()->where('status', 'pending')->count())
                ->badgeColor('warning'),

            'confirmed' => Tab::make(__('owner_booking.pages.list.tabs.confirmed'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'confirmed'))
                ->badge(fn() => $baseQuery()->where('status', 'confirmed')->count())
                ->badgeColor('success'),

            'upcoming' => Tab::make(__('owner_booking.pages.list.tabs.upcoming'))
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('booking_date', '>=', now()->toDateString())
                    ->whereIn('status', ['pending', 'confirmed'])
                )
                ->badge(fn() => $baseQuery()
                    ->where('booking_date', '>=', now()->toDateString())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count())
                ->badgeColor('info'),

            'completed' => Tab::make(__('owner_booking.pages.list.tabs.completed'))
                ->icon('heroicon-o-check-badge')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                ->badge(fn() => $baseQuery()->where('status', 'completed')->count())
                ->badgeColor('info'),

            'cancelled' => Tab::make(__('owner_booking.pages.list.tabs.cancelled'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled'))
                ->badge(fn() => $baseQuery()->where('status', 'cancelled')->count())
                ->badgeColor('danger'),
        ];
    }

    /**
     * Get the header widgets for this page.
     */
    protected function getHeaderWidgets(): array
    {
        return [
            BookingResource\Widgets\BookingStatsWidget::class,
        ];
    }
}
