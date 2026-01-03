<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\EarningsResource\Pages;

use App\Filament\Owner\Resources\EarningsResource;
use App\Models\Booking;
use App\Models\Hall;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

/**
 * ListEarnings Page for Owner Panel
 *
 * Displays earnings list with summary statistics and filtering.
 *
 * Features:
 * - Summary cards showing totals
 * - Tabbed navigation (All, This Month, Last Month)
 * - Export functionality
 * - Period-based filtering
 *
 * @package App\Filament\Owner\Resources\EarningsResource\Pages
 */
class ListEarnings extends ListRecords
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = EarningsResource::class;

    /**
     * Get the page title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return __('owner.earnings.title');
    }

    /**
     * Get the page heading.
     *
     * @return string
     */
    public function getHeading(): string
    {
        return __('owner.earnings.heading');
    }

    /**
     * Get the page subheading with summary.
     *
     * @return string|null
     */
    public function getSubheading(): ?string
    {
        $stats = $this->getEarningsStats();

        return __('owner.earnings.subheading', [
            'total' => number_format($stats['total_earnings'], 3),
            'month' => number_format($stats['month_earnings'], 3),
        ]);
    }

    /**
     * Get header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // Generate PDF Report
            Actions\Action::make('generateReport')
                ->label(__('owner.earnings.generate_report'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Section::make(__('owner.earnings.report_period'))
                        ->schema([
                            \Filament\Forms\Components\Grid::make(2)
                                ->schema([
                                    \Filament\Forms\Components\DatePicker::make('from_date')
                                        ->label(__('owner.earnings.from_date'))
                                        ->default(now()->startOfMonth())
                                        ->required()
                                        ->native(false),

                                    \Filament\Forms\Components\DatePicker::make('to_date')
                                        ->label(__('owner.earnings.to_date'))
                                        ->default(now())
                                        ->required()
                                        ->native(false),
                                ]),

                            \Filament\Forms\Components\Select::make('hall_id')
                                ->label(__('owner.earnings.select_hall'))
                                ->options(fn (): array => Hall::where('owner_id', Auth::id())
                                    ->get()
                                    ->mapWithKeys(fn ($hall): array => [
                                        $hall->id => $hall->name[app()->getLocale()] ?? $hall->name['en'],
                                    ])
                                    ->toArray())
                                ->placeholder(__('owner.earnings.all_halls')),

                            \Filament\Forms\Components\CheckboxList::make('include')
                                ->label(__('owner.earnings.include_in_report'))
                                ->options([
                                    'summary' => __('owner.earnings.summary'),
                                    'details' => __('owner.earnings.booking_details'),
                                    'breakdown' => __('owner.earnings.hall_breakdown'),
                                    'chart' => __('owner.earnings.chart'),
                                ])
                                ->default(['summary', 'details'])
                                ->columns(2),
                        ]),
                ])
                ->action(function (array $data): void {
                    $this->generateEarningsReport($data);
                }),

            // Export to Excel (CSV)
            Actions\Action::make('exportExcel')
                ->label(__('owner.earnings.export_excel'))
                ->icon('heroicon-o-table-cells')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Section::make(__('owner.earnings.export_settings'))
                        ->schema([
                            \Filament\Forms\Components\Grid::make(2)
                                ->schema([
                                    \Filament\Forms\Components\DatePicker::make('from_date')
                                        ->label(__('owner.earnings.from_date'))
                                        ->default(now()->startOfMonth())
                                        ->required()
                                        ->native(false),

                                    \Filament\Forms\Components\DatePicker::make('to_date')
                                        ->label(__('owner.earnings.to_date'))
                                        ->default(now())
                                        ->required()
                                        ->native(false),
                                ]),

                            \Filament\Forms\Components\Select::make('hall_id')
                                ->label(__('owner.earnings.select_hall'))
                                ->options(fn (): array => Hall::where('owner_id', Auth::id())
                                    ->get()
                                    ->mapWithKeys(fn ($hall): array => [
                                        $hall->id => $hall->name[app()->getLocale()] ?? $hall->name['en'],
                                    ])
                                    ->toArray())
                                ->placeholder(__('owner.earnings.all_halls')),

                            \Filament\Forms\Components\CheckboxList::make('columns')
                                ->label(__('owner.earnings.include_columns'))
                                ->options([
                                    'booking_number' => __('owner.earnings.columns.booking_number'),
                                    'hall' => __('owner.earnings.columns.hall'),
                                    'customer' => __('owner.earnings.columns.customer'),
                                    'date' => __('owner.earnings.columns.date'),
                                    'slot' => __('owner.earnings.columns.slot'),
                                    'hall_price' => __('owner.earnings.columns.hall_price'),
                                    'services_price' => __('owner.earnings.columns.services_price'),
                                    'gross_amount' => __('owner.earnings.columns.gross_amount'),
                                    'commission' => __('owner.earnings.columns.commission'),
                                    'net_earnings' => __('owner.earnings.columns.net_earnings'),
                                ])
                                ->default([
                                    'booking_number',
                                    'hall',
                                    'date',
                                    'gross_amount',
                                    'commission',
                                    'net_earnings',
                                ])
                                ->columns(2)
                                ->bulkToggleable(),
                        ]),
                ])
                ->action(function (array $data): void {
                    $this->exportEarningsToCSV($data);
                }),
        ];
    }

    /**
     * Get tabs for period filtering.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $user = Auth::user();

        return [
            'all' => Tab::make(__('owner.earnings.tab_all'))
                ->icon('heroicon-m-banknotes')
                ->badge(fn (): int => $this->getTabCount('all')),

            'this_month' => Tab::make(__('owner.earnings.tab_this_month'))
                ->icon('heroicon-m-calendar')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->whereMonth('booking_date', now()->month)
                    ->whereYear('booking_date', now()->year))
                ->badge(fn (): int => $this->getTabCount('this_month')),

            'last_month' => Tab::make(__('owner.earnings.tab_last_month'))
                ->icon('heroicon-m-calendar-days')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->whereMonth('booking_date', now()->subMonth()->month)
                    ->whereYear('booking_date', now()->subMonth()->year))
                ->badge(fn (): int => $this->getTabCount('last_month')),

            'this_year' => Tab::make(__('owner.earnings.tab_this_year'))
                ->icon('heroicon-m-chart-bar')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->whereYear('booking_date', now()->year))
                ->badge(fn (): int => $this->getTabCount('this_year')),
        ];
    }

    /**
     * Get header widgets.
     *
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            EarningsResource\Widgets\EarningsSummaryWidget::class,
        ];
    }

    /**
     * Get earnings statistics for subheading.
     *
     * @return array<string, float>
     */
    protected function getEarningsStats(): array
    {
        $user = Auth::user();

        // Total earnings
        $totalEarnings = (float) Booking::whereHas('hall', function ($q) use ($user): void {
            $q->where('owner_id', $user->id);
        })
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('owner_payout');

        // This month earnings
        $monthEarnings = (float) Booking::whereHas('hall', function ($q) use ($user): void {
            $q->where('owner_id', $user->id);
        })
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->whereMonth('booking_date', now()->month)
            ->whereYear('booking_date', now()->year)
            ->sum('owner_payout');

        return [
            'total_earnings' => $totalEarnings,
            'month_earnings' => $monthEarnings,
        ];
    }

    /**
     * Get tab count for badges.
     *
     * @param string $tab Tab identifier
     * @return int Count
     */
    protected function getTabCount(string $tab): int
    {
        $user = Auth::user();

        $query = Booking::whereHas('hall', function ($q) use ($user): void {
            $q->where('owner_id', $user->id);
        })
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid');

        return match ($tab) {
            'this_month' => (int) $query
                ->whereMonth('booking_date', now()->month)
                ->whereYear('booking_date', now()->year)
                ->count(),
            'last_month' => (int) $query
                ->whereMonth('booking_date', now()->subMonth()->month)
                ->whereYear('booking_date', now()->subMonth()->year)
                ->count(),
            'this_year' => (int) $query
                ->whereYear('booking_date', now()->year)
                ->count(),
            default => (int) $query->count(),
        };
    }

    /**
     * Generate earnings PDF report.
     *
     * @param array $data Form data
     * @return void
     */
    protected function generateEarningsReport(array $data): void
    {
        try {
            $user = Auth::user();
            $fromDate = $data['from_date'];
            $toDate = $data['to_date'];
            $hallId = $data['hall_id'] ?? null;
            $includes = $data['include'] ?? ['summary', 'details'];

            // Build query
            $query = Booking::whereHas('hall', function ($q) use ($user, $hallId): void {
                $q->where('owner_id', $user->id);
                if ($hallId) {
                    $q->where('id', $hallId);
                }
            })
                ->whereBetween('booking_date', [$fromDate, $toDate])
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->with(['hall', 'extraServices'])
                ->orderBy('booking_date', 'desc');

            $bookings = $query->get();

            // Calculate statistics
            $stats = [
                'total_bookings' => $bookings->count(),
                'gross_revenue' => (float) $bookings->sum('total_amount'),
                'total_commission' => (float) $bookings->sum('commission_amount'),
                'net_earnings' => (float) $bookings->sum('owner_payout'),
                'hall_revenue' => (float) $bookings->sum('hall_price'),
                'services_revenue' => (float) $bookings->sum('services_price'),
            ];

            // Hall breakdown
            $hallBreakdown = $bookings->groupBy('hall_id')->map(function ($hallBookings) {
                $hall = $hallBookings->first()->hall;
                $hallName = is_array($hall->name)
                    ? ($hall->name[app()->getLocale()] ?? $hall->name['en'])
                    : $hall->name;

                return [
                    'hall_name' => $hallName,
                    'bookings_count' => $hallBookings->count(),
                    'gross_revenue' => (float) $hallBookings->sum('total_amount'),
                    'commission' => (float) $hallBookings->sum('commission_amount'),
                    'net_earnings' => (float) $hallBookings->sum('owner_payout'),
                ];
            })->values();

            // Monthly breakdown
            $monthlyBreakdown = $bookings->groupBy(function ($booking) {
                return $booking->booking_date->format('Y-m');
            })->map(function ($monthBookings) {
                return [
                    'month' => $monthBookings->first()->booking_date->format('F Y'),
                    'bookings' => $monthBookings->count(),
                    'gross' => (float) $monthBookings->sum('total_amount'),
                    'commission' => (float) $monthBookings->sum('commission_amount'),
                    'net' => (float) $monthBookings->sum('owner_payout'),
                ];
            })->values();

            // Get owner info
            $hallOwner = $user->hallOwner;

            // Generate PDF
            $pdf = Pdf::loadView('pdf.owner-earnings-report', [
                'owner' => $user,
                'hallOwner' => $hallOwner,
                'bookings' => in_array('details', $includes) ? $bookings : collect(),
                'stats' => $stats,
                'hallBreakdown' => in_array('breakdown', $includes) ? $hallBreakdown : collect(),
                'monthlyBreakdown' => $monthlyBreakdown,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'includes' => $includes,
                'generatedAt' => now(),
            ])->setPaper('a4');

            // Ensure directory exists
            if (!Storage::disk('public')->exists('reports')) {
                Storage::disk('public')->makeDirectory('reports');
            }

            // Save file
            $filename = 'earnings-report-' . $user->id . '-' . now()->format('Y-m-d-His') . '.pdf';
            $filepath = 'reports/' . $filename;

            Storage::disk('public')->put($filepath, $pdf->output());

            // Download notification with link
            Notification::make()
                ->success()
                ->title(__('owner.earnings.report_generated'))
                ->body(__('owner.earnings.report_generated_desc', [
                    'bookings' => $stats['total_bookings'],
                    'earnings' => number_format($stats['net_earnings'], 3),
                ]))
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label(__('owner.actions.download'))
                        ->url(Storage::disk('public')->url($filepath))
                        ->openUrlInNewTab(),
                ])
                ->persistent()
                ->send();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Earnings report generation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->danger()
                ->title(__('owner.earnings.report_failed'))
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Export earnings data to CSV file.
     *
     * Creates an Excel-compatible CSV file with:
     * - UTF-8 BOM for proper character encoding
     * - Configurable columns
     * - Date range filtering
     * - Hall filtering
     *
     * @param array $data Form data containing filters and column selection
     * @return void
     */
    protected function exportEarningsToCSV(array $data): void
    {
        try {
            $user = Auth::user();
            $fromDate = $data['from_date'];
            $toDate = $data['to_date'];
            $hallId = $data['hall_id'] ?? null;
            $selectedColumns = $data['columns'] ?? [
                'booking_number',
                'hall',
                'date',
                'gross_amount',
                'commission',
                'net_earnings',
            ];

            // Build query for earnings
            $query = Booking::whereHas('hall', function ($q) use ($user, $hallId): void {
                $q->where('owner_id', $user->id);
                if ($hallId) {
                    $q->where('id', $hallId);
                }
            })
                ->whereBetween('booking_date', [$fromDate, $toDate])
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->with(['hall', 'extraServices'])
                ->orderBy('booking_date', 'desc');

            $bookings = $query->get();

            if ($bookings->isEmpty()) {
                Notification::make()
                    ->warning()
                    ->title(__('owner.earnings.no_data'))
                    ->body(__('owner.earnings.no_data_desc'))
                    ->send();

                return;
            }

            // Ensure exports directory exists
            $exportPath = 'exports/earnings';
            if (!Storage::disk('public')->exists($exportPath)) {
                Storage::disk('public')->makeDirectory($exportPath);
            }

            // Generate filename
            $filename = 'earnings_export_' . $user->id . '_' . now()->format('Y_m_d_His') . '.csv';
            $fullPath = storage_path('app/public/' . $exportPath . '/' . $filename);

            // Open file for writing
            $file = fopen($fullPath, 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Build headers based on selected columns
            $headers = $this->buildCSVHeaders($selectedColumns);
            fputcsv($file, $headers);

            // Calculate totals for summary row
            $totals = [
                'hall_price' => 0.0,
                'services_price' => 0.0,
                'gross_amount' => 0.0,
                'commission' => 0.0,
                'net_earnings' => 0.0,
            ];

            // Write data rows
            foreach ($bookings as $booking) {
                $row = $this->buildCSVRow($booking, $selectedColumns);
                fputcsv($file, $row);

                // Accumulate totals
                $totals['hall_price'] += (float) $booking->hall_price;
                $totals['services_price'] += (float) $booking->services_price;
                $totals['gross_amount'] += (float) $booking->total_amount;
                $totals['commission'] += (float) $booking->commission_amount;
                $totals['net_earnings'] += (float) $booking->owner_payout;
            }

            // Add empty row before totals
            fputcsv($file, []);

            // Add totals row
            $totalsRow = $this->buildTotalsRow($selectedColumns, $totals, $bookings->count());
            fputcsv($file, $totalsRow);

            fclose($file);

            // Log the export
            \Illuminate\Support\Facades\Log::info('Owner earnings exported', [
                'user_id' => $user->id,
                'count' => $bookings->count(),
                'date_range' => [$fromDate, $toDate],
                'hall_id' => $hallId,
                'filename' => $filename,
            ]);

            // Send success notification with download link
            Notification::make()
                ->success()
                ->title(__('owner.earnings.export_success'))
                ->body(__('owner.earnings.export_success_desc', [
                    'count' => $bookings->count(),
                    'total' => number_format($totals['net_earnings'], 3),
                ]))
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label(__('owner.actions.download'))
                        ->url(Storage::disk('public')->url($exportPath . '/' . $filename))
                        ->openUrlInNewTab(),
                ])
                ->persistent()
                ->send();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Owner earnings export failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->danger()
                ->title(__('owner.earnings.export_failed'))
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Build CSV headers based on selected columns.
     *
     * @param array $selectedColumns Column keys to include
     * @return array Header row
     */
    protected function buildCSVHeaders(array $selectedColumns): array
    {
        $columnLabels = [
            'booking_number' => __('owner.earnings.columns.booking_number'),
            'hall' => __('owner.earnings.columns.hall'),
            'customer' => __('owner.earnings.columns.customer'),
            'date' => __('owner.earnings.columns.date'),
            'slot' => __('owner.earnings.columns.slot'),
            'hall_price' => __('owner.earnings.columns.hall_price') . ' (OMR)',
            'services_price' => __('owner.earnings.columns.services_price') . ' (OMR)',
            'gross_amount' => __('owner.earnings.columns.gross_amount') . ' (OMR)',
            'commission' => __('owner.earnings.columns.commission') . ' (OMR)',
            'net_earnings' => __('owner.earnings.columns.net_earnings') . ' (OMR)',
        ];

        $headers = [];
        foreach ($selectedColumns as $column) {
            if (isset($columnLabels[$column])) {
                $headers[] = $columnLabels[$column];
            }
        }

        return $headers;
    }

    /**
     * Build CSV row for a booking based on selected columns.
     *
     * @param Booking $booking The booking record
     * @param array $selectedColumns Column keys to include
     * @return array Data row
     */
    protected function buildCSVRow(Booking $booking, array $selectedColumns): array
    {
        // Get hall name (handling translatable field)
        $hallName = is_array($booking->hall->name)
            ? ($booking->hall->name[app()->getLocale()] ?? $booking->hall->name['en'] ?? '')
            : $booking->hall->name;

        // Get time slot label
        $slotLabels = [
            'morning' => __('slots.morning'),
            'afternoon' => __('slots.afternoon'),
            'evening' => __('slots.evening'),
            'full_day' => __('slots.full_day'),
        ];
        $slotLabel = $slotLabels[$booking->time_slot] ?? $booking->time_slot;

        // Column values mapping
        $columnValues = [
            'booking_number' => $booking->booking_number,
            'hall' => $hallName,
            'customer' => $booking->customer_name,
            'date' => $booking->booking_date->format('Y-m-d'),
            'slot' => $slotLabel,
            'hall_price' => number_format((float) $booking->hall_price, 3, '.', ''),
            'services_price' => number_format((float) $booking->services_price, 3, '.', ''),
            'gross_amount' => number_format((float) $booking->total_amount, 3, '.', ''),
            'commission' => number_format((float) $booking->commission_amount, 3, '.', ''),
            'net_earnings' => number_format((float) $booking->owner_payout, 3, '.', ''),
        ];

        $row = [];
        foreach ($selectedColumns as $column) {
            if (isset($columnValues[$column])) {
                $row[] = $columnValues[$column];
            }
        }

        return $row;
    }

    /**
     * Build totals row for CSV export.
     *
     * @param array $selectedColumns Column keys to include
     * @param array $totals Calculated totals
     * @param int $count Total record count
     * @return array Totals row
     */
    protected function buildTotalsRow(array $selectedColumns, array $totals, int $count): array
    {
        $row = [];
        $isFirstColumn = true;

        foreach ($selectedColumns as $column) {
            if ($isFirstColumn) {
                $row[] = __('owner.earnings.totals') . ' (' . $count . ' ' . __('owner.earnings.bookings') . ')';
                $isFirstColumn = false;
                continue;
            }

            // Add totals for numeric columns
            switch ($column) {
                case 'hall_price':
                    $row[] = number_format($totals['hall_price'], 3, '.', '');
                    break;
                case 'services_price':
                    $row[] = number_format($totals['services_price'], 3, '.', '');
                    break;
                case 'gross_amount':
                    $row[] = number_format($totals['gross_amount'], 3, '.', '');
                    break;
                case 'commission':
                    $row[] = number_format($totals['commission'], 3, '.', '');
                    break;
                case 'net_earnings':
                    $row[] = number_format($totals['net_earnings'], 3, '.', '');
                    break;
                default:
                    $row[] = '';
            }
        }

        return $row;
    }
}
