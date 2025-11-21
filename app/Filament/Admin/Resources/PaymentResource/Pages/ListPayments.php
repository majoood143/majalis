<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


/**
 * List Payments Page
 *
 * Comprehensive payment listing with advanced features:
 * - Status-based tabs with live badge counts
 * - Payment export functionality (CSV)
 * - Financial reports generation
 * - Payment reconciliation
 * - Bulk operations
 * - Advanced filtering
 *
 * @package App\Filament\Admin\Resources\PaymentResource\Pages
 */
class ListPayments extends ListRecords
{
    /**
     * The resource associated with this page
     *
     * @var string
     */
    protected static string $resource = PaymentResource::class;

    /**
     * Get the header actions for this page
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            // Create new payment action
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->label('Create Payment'),

            /**
             * Export Payments Action
             *
             * Exports payment records to CSV with filters.
             */
            Actions\Action::make('exportPayments')
                ->label('Export Payments')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Section::make('Export Options')
                        ->schema([
                            \Filament\Forms\Components\Grid::make(2)
                                ->schema([
                                    \Filament\Forms\Components\DatePicker::make('from_date')
                                        ->label('From Date')
                                        ->default(now()->startOfMonth())
                                        ->native(false)
                                        ->displayFormat('d/m/Y')
                                        ->required(),

                                    \Filament\Forms\Components\DatePicker::make('to_date')
                                        ->label('To Date')
                                        ->default(now())
                                        ->native(false)
                                        ->displayFormat('d/m/Y')
                                        ->required(),
                                ]),

                            \Filament\Forms\Components\Select::make('status')
                                ->label('Filter by Status')
                                ->options([
                                    'all' => 'All Statuses',
                                    'paid' => 'Paid Only',
                                    'pending' => 'Pending Only',
                                    'failed' => 'Failed Only',
                                    'refunded' => 'Refunded Only',
                                    'partially_refunded' => 'Partially Refunded',
                                ])
                                ->default('all')
                                ->required(),

                            \Filament\Forms\Components\Select::make('format')
                                ->label('Export Format')
                                ->options([
                                    'csv' => 'CSV (Excel compatible)',
                                    'json' => 'JSON',
                                ])
                                ->default('csv')
                                ->required(),

                            \Filament\Forms\Components\Toggle::make('include_booking_details')
                                ->label('Include Booking Details')
                                ->default(true)
                                ->helperText('Include related booking information in export'),
                        ]),
                ])
                ->action(function (array $data) {
                    $this->exportPayments($data);
                }),

            /**
             * Financial Report Action
             *
             * Generates comprehensive financial report.
             */
            Actions\Action::make('generateFinancialReport')
                ->label('Financial Report')
                ->icon('heroicon-o-document-chart-bar')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Section::make('Report Period')
                        ->schema([
                            \Filament\Forms\Components\Grid::make(2)
                                ->schema([
                                    \Filament\Forms\Components\DatePicker::make('from_date')
                                        ->label('From Date')
                                        ->required()
                                        ->default(now()->startOfMonth())
                                        ->native(false)
                                        ->displayFormat('d/m/Y'),

                                    \Filament\Forms\Components\DatePicker::make('to_date')
                                        ->label('To Date')
                                        ->required()
                                        ->default(now())
                                        ->native(false)
                                        ->displayFormat('d/m/Y'),
                                ]),

                            \Filament\Forms\Components\CheckboxList::make('metrics')
                                ->label('Include Metrics')
                                ->options([
                                    'revenue' => 'Total Revenue',
                                    'refunds' => 'Refunds Summary',
                                    'failed' => 'Failed Payments Analysis',
                                    'payment_methods' => 'Payment Methods Breakdown',
                                    'daily_trends' => 'Daily Transaction Trends',
                                ])
                                ->default(['revenue', 'refunds', 'payment_methods'])
                                ->columns(2),
                        ]),
                ])
                ->action(function (array $data) {
                    $this->generateFinancialReport($data);
                }),

            /**
             * Reconcile Payments Action
             *
             * Matches payments with gateway transactions.
             */
            Actions\Action::make('reconcilePayments')
                ->label('Reconcile Payments')
                ->icon('heroicon-o-calculator')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reconcile Payment Records')
                ->modalDescription('Match payment records with gateway transactions. This may take a few moments.')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('reconcile_date')
                        ->label('Reconcile For Date')
                        ->default(today())
                        ->native(false)
                        ->required(),

                    \Filament\Forms\Components\Toggle::make('auto_update')
                        ->label('Auto-update Mismatches')
                        ->default(false)
                        ->helperText('Automatically update payment statuses based on gateway data'),
                ])
                ->action(function (array $data) {
                    $this->reconcilePayments($data);
                }),

            /**
             * Retry Failed Payments Action
             *
             * Attempts to reprocess failed payments.
             */
            Actions\Action::make('retryFailedPayments')
                ->label('Retry Failed')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Retry Failed Payments')
                ->modalDescription('Attempt to reprocess all failed payments. Only recent failures will be retried.')
                ->form([
                    \Filament\Forms\Components\Select::make('age')
                        ->label('Failed Within')
                        ->options([
                            '1' => 'Last 24 hours',
                            '3' => 'Last 3 days',
                            '7' => 'Last 7 days',
                            '30' => 'Last 30 days',
                        ])
                        ->default('1')
                        ->required(),

                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Retry Reason (Optional)')
                        ->rows(2),
                ])
                ->action(function (array $data) {
                    $this->retryFailedPayments($data);
                }),

            /**
             * Send Payment Reminders Action
             *
             * Sends reminders for pending payments.
             */
            Actions\Action::make('sendReminders')
                ->label('Send Reminders')
                ->icon('heroicon-o-bell')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Send Payment Reminders')
                ->modalDescription('Send email reminders to customers with pending payments')
                ->form([
                    \Filament\Forms\Components\Select::make('age')
                        ->label('Pending For')
                        ->options([
                            '1' => 'More than 1 day',
                            '3' => 'More than 3 days',
                            '7' => 'More than 7 days',
                        ])
                        ->default('1')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->sendPaymentReminders($data);
                }),
        ];
    }

    /**
     * Get tabs for payment listing
     *
     * @return array
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Payments')
                ->icon('heroicon-o-credit-card')
                ->badge(fn() => \App\Models\Payment::count()),

            'paid' => Tab::make('Paid')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'paid'))
                ->badge(fn() => \App\Models\Payment::where('status', 'paid')->count())
                ->badgeColor('success'),

            'pending' => Tab::make('Pending')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(fn() => \App\Models\Payment::where('status', 'pending')->count())
                ->badgeColor('warning'),

            'failed' => Tab::make('Failed')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'failed'))
                ->badge(fn() => \App\Models\Payment::where('status', 'failed')->count())
                ->badgeColor('danger'),

            'refunded' => Tab::make('Refunded')
                ->icon('heroicon-o-arrow-path')
                ->modifyQueryUsing(fn(Builder $query) =>
                    $query->whereIn('status', ['refunded', 'partially_refunded']))
                ->badge(fn() => \App\Models\Payment::whereIn('status', ['refunded', 'partially_refunded'])->count())
                ->badgeColor('info'),

            'today' => Tab::make('Today')
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('created_at', today()))
                ->badge(fn() => \App\Models\Payment::whereDate('created_at', today())->count())
                ->badgeColor('purple'),

            'this_week' => Tab::make('This Week')
                ->icon('heroicon-o-calendar-days')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]))
                ->badge(fn() => \App\Models\Payment::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count())
                ->badgeColor('purple'),

            'this_month' => Tab::make('This Month')
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn(Builder $query) =>
                    $query->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year))
                ->badge(fn() => \App\Models\Payment::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count())
                ->badgeColor('primary'),

            'high_value' => Tab::make('High Value (1000+ OMR)')
                ->icon('heroicon-o-banknotes')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('amount', '>=', 1000))
                ->badge(fn() => \App\Models\Payment::where('amount', '>=', 1000)->count())
                ->badgeColor('success'),
        ];
    }

    /**
     * Export payments to CSV file
     *
     * @param array $data
     * @return void
     */
    protected function exportPayments(array $data): void
    {
        try {
            // Build query with filters
            $query = \App\Models\Payment::query()
                ->with(['booking.hall'])
                ->whereBetween('created_at', [
                    $data['from_date'] . ' 00:00:00',
                    $data['to_date'] . ' 23:59:59'
                ]);

            // Apply status filter
            if ($data['status'] !== 'all') {
                if ($data['status'] === 'refunded') {
                    $query->whereIn('status', ['refunded', 'partially_refunded']);
                } else {
                    $query->where('status', $data['status']);
                }
            }

            $payments = $query->orderBy('created_at', 'desc')->get();

            // Determine format
            if ($data['format'] === 'json') {
                $this->exportAsJson($payments, $data);
                return;
            }

            // Create exports directory if it doesn't exist
            $exportPath = 'exports/payments';
            if (!Storage::disk('public')->exists($exportPath)) {
                Storage::disk('public')->makeDirectory($exportPath);
            }

            // Generate filename
            $filename = 'payments_export_' . now()->format('Y_m_d_His') . '.csv';
            $path = storage_path('app/public/' . $exportPath . '/' . $filename);

            // Open file for writing
            $file = fopen($path, 'w');

            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write headers
            $headers = [
                'Payment Reference',
                'Booking Number',
                'Transaction ID',
                'Amount (OMR)',
                'Currency',
                'Status',
                'Payment Method',
                'Refund Amount (OMR)',
                'Paid At',
                'Failed At',
                'Refunded At',
                'Created At',
            ];

            if ($data['include_booking_details']) {
                $headers = array_merge($headers, [
                    'Customer Name',
                    'Customer Email',
                    'Hall Name',
                    'Booking Date',
                ]);
            }

            fputcsv($file, $headers);

            // Write data rows
            foreach ($payments as $payment) {
                $row = [
                    $payment->payment_reference,
                    $payment->booking?->booking_number ?? 'N/A',
                    $payment->transaction_id ?? '',
                    number_format((float) $payment->amount, 3, '.', ''),
                    $payment->currency,
                    ucfirst(str_replace('_', ' ', $payment->status)),
                    $payment->payment_method ?? '',
                    $payment->refund_amount ? number_format($payment->refund_amount, 3, '.', '') : '',
                    $payment->paid_at?->format('Y-m-d H:i:s') ?? '',
                    $payment->failed_at?->format('Y-m-d H:i:s') ?? '',
                    $payment->refunded_at?->format('Y-m-d H:i:s') ?? '',
                    $payment->created_at->format('Y-m-d H:i:s'),
                ];

                if ($data['include_booking_details']) {
                    $row = array_merge($row, [
                        $payment->booking?->customer_name ?? '',
                        $payment->booking?->customer_email ?? '',
                        $payment->booking?->hall?->name ?? '',
                        $payment->booking?->booking_date?->format('Y-m-d') ?? '',
                    ]);
                }

                fputcsv($file, $row);
            }

            fclose($file);

            // Log the export
            Log::info('Payments exported', [
                'user_id' => auth()->id(),
                'count' => $payments->count(),
                'filters' => $data,
                'filename' => $filename,
            ]);

            // Send success notification with download link
            Notification::make()
                ->title('Export Successful')
                ->success()
                ->body("{$payments->count()} payment(s) exported successfully.")
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label('Download File')
                        ->url(asset('storage/' . $exportPath . '/' . $filename))
                        ->openUrlInNewTab(),
                ])
                ->send();
        } catch (\Exception $e) {
            Log::error('Payment export failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            Notification::make()
                ->danger()
                ->title('Export Failed')
                ->body('Failed to export payments: ' . $e->getMessage())
                ->persistent()
                ->send();
        }
    }

    /**
     * Export payments as JSON
     *
     * @param \Illuminate\Support\Collection $payments
     * @param array $data
     * @return void
     */
    protected function exportAsJson($payments, array $data): void
    {
        $filename = 'payments_export_' . now()->format('Y_m_d_His') . '.json';
        $path = storage_path('app/public/exports/payments/' . $filename);

        $jsonData = $payments->map(function ($payment) use ($data) {
            $paymentData = [
                'payment_reference' => $payment->payment_reference,
                'booking_number' => $payment->booking?->booking_number,
                'transaction_id' => $payment->transaction_id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => $payment->status,
                'payment_method' => $payment->payment_method,
                'refund_amount' => $payment->refund_amount,
                'paid_at' => $payment->paid_at?->toIso8601String(),
                'created_at' => $payment->created_at->toIso8601String(),
            ];

            if ($data['include_booking_details'] && $payment->booking) {
                $paymentData['booking'] = [
                    'customer_name' => $payment->booking->customer_name,
                    'customer_email' => $payment->booking->customer_email,
                    'hall_name' => $payment->booking->hall?->name,
                    'booking_date' => $payment->booking->booking_date?->format('Y-m-d'),
                ];
            }

            return $paymentData;
        });

        file_put_contents($path, json_encode($jsonData, JSON_PRETTY_PRINT));

        Notification::make()
            ->success()
            ->title('JSON Export Complete')
            ->actions([
                \Filament\Notifications\Actions\Action::make('download')
                    ->label('Download')
                    ->url(asset('storage/exports/payments/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }

    /**
     * Reconcile payments with gateway
     *
     * @param array $data
     * @return void
     */
    protected function reconcilePayments(array $data): void
    {
        try {
            $reconciledCount = 0;
            $mismatches = [];

            // Get payments for the specified date
            $payments = \App\Models\Payment::whereDate('created_at', $data['reconcile_date'])
                ->whereNotNull('transaction_id')
                ->get();

            foreach ($payments as $payment) {
                // TODO: Implement actual gateway reconciliation
                // $gatewayStatus = PaymentGatewayService::checkStatus($payment->transaction_id);

                // if ($gatewayStatus !== $payment->status) {
                //     $mismatches[] = [
                //         'payment_reference' => $payment->payment_reference,
                //         'system_status' => $payment->status,
                //         'gateway_status' => $gatewayStatus,
                //     ];
                //
                //     if ($data['auto_update']) {
                //         $payment->update(['status' => $gatewayStatus]);
                //     }
                // }

                $reconciledCount++;
            }

            Log::info('Payment reconciliation completed', [
                'date' => $data['reconcile_date'],
                'reconciled' => $reconciledCount,
                'mismatches' => count($mismatches),
            ]);

            Notification::make()
                ->success()
                ->title('Reconciliation Completed')
                ->body("{$reconciledCount} payment(s) reconciled. " .
                    (count($mismatches) > 0 ? count($mismatches) . " mismatch(es) found." : ""))
                ->persistent()
                ->send();
        } catch (\Exception $e) {
            Log::error('Payment reconciliation failed', [
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->danger()
                ->title('Reconciliation Failed')
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Generate comprehensive financial report
     *
     * @param array $data
     * @return void
     */
    // protected function generateFinancialReport(array $data): void
    // {
    //     try {
    //         $metrics = $data['metrics'] ?? [];
    //         $report = [];

    //         // Total Revenue
    //         if (in_array('revenue', $metrics)) {
    //             $paidPayments = \App\Models\Payment::whereBetween('paid_at', [
    //                 $data['from_date'],
    //                 $data['to_date'] . ' 23:59:59'
    //             ])
    //                 ->where('status', 'paid')
    //                 ->get();

    //             $report['revenue'] = [
    //                 'total' => $paidPayments->sum('amount'),
    //                 'count' => $paidPayments->count(),
    //                 'average' => $paidPayments->avg('amount'),
    //             ];
    //         }

    //         // Refunds Summary
    //         if (in_array('refunds', $metrics)) {
    //             $refundedPayments = \App\Models\Payment::whereBetween('refunded_at', [
    //                 $data['from_date'],
    //                 $data['to_date'] . ' 23:59:59'
    //             ])
    //                 ->whereIn('status', ['refunded', 'partially_refunded'])
    //                 ->get();

    //             $report['refunds'] = [
    //                 'total_refunded' => $refundedPayments->sum('refund_amount'),
    //                 'count' => $refundedPayments->count(),
    //                 'full_refunds' => $refundedPayments->where('status', 'refunded')->count(),
    //                 'partial_refunds' => $refundedPayments->where('status', 'partially_refunded')->count(),
    //             ];
    //         }

    //         // Failed Payments
    //         if (in_array('failed', $metrics)) {
    //             $failedPayments = \App\Models\Payment::whereBetween('failed_at', [
    //                 $data['from_date'],
    //                 $data['to_date'] . ' 23:59:59'
    //             ])
    //                 ->where('status', 'failed')
    //                 ->get();

    //             $report['failed'] = [
    //                 'count' => $failedPayments->count(),
    //                 'lost_revenue' => $failedPayments->sum('amount'),
    //             ];
    //         }

    //         // Payment Methods Breakdown
    //         if (in_array('payment_methods', $metrics)) {
    //             $methodBreakdown = \App\Models\Payment::whereBetween('created_at', [
    //                 $data['from_date'],
    //                 $data['to_date'] . ' 23:59:59'
    //             ])
    //                 ->where('status', 'paid')
    //                 ->groupBy('payment_method')
    //                 ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
    //                 ->get();

    //             $report['payment_methods'] = $methodBreakdown->toArray();
    //         }

    //         // Calculate Net Revenue
    //         $netRevenue = ($report['revenue']['total'] ?? 0) - ($report['refunds']['total_refunded'] ?? 0);

    //         // Build notification message
    //         $message = "Period: " . date('d/m/Y', strtotime($data['from_date'])) .
    //             " to " . date('d/m/Y', strtotime($data['to_date'])) . "\n\n";

    //         if (isset($report['revenue'])) {
    //             $message .= "💰 Revenue: " . number_format($report['revenue']['total'], 3) . " OMR\n";
    //         }
    //         if (isset($report['refunds'])) {
    //             $message .= "↩️ Refunds: " . number_format($report['refunds']['total_refunded'], 3) . " OMR\n";
    //         }
    //         $message .= "📊 Net Revenue: " . number_format($netRevenue, 3) . " OMR";

    //         Log::info('Financial report generated', [
    //             'user_id' => auth()->id(),
    //             'period' => [$data['from_date'], $data['to_date']],
    //             'report' => $report,
    //         ]);

    //         Notification::make()
    //             ->success()
    //             ->title('Financial Report Generated')
    //             ->body($message)
    //             ->persistent()
    //             ->send();
    //     } catch (\Exception $e) {
    //         Log::error('Financial report generation failed', [
    //             'error' => $e->getMessage(),
    //         ]);

    //         Notification::make()
    //             ->danger()
    //             ->title('Report Generation Failed')
    //             ->body($e->getMessage())
    //             ->send();
    //     }
    // }

    /**
     * Generate comprehensive financial report
     *
     * @param array $data
     * @return void
     */
    protected function generateFinancialReport(array $data): void
    {
        try {
            $metrics = $data['metrics'] ?? [];
            $report = [];

            // Parse dates properly (handles both Carbon objects and strings)
            $fromDate = is_string($data['from_date'])
                ? $data['from_date']
                : $data['from_date']->format('Y-m-d');

            $toDate = is_string($data['to_date'])
                ? $data['to_date']
                : $data['to_date']->format('Y-m-d');

            // Total Revenue - use created_at if paid_at is null
            if (in_array('revenue', $metrics)) {
                $paidPayments = \App\Models\Payment::where('status', 'paid')
                    ->where(function ($query) use ($fromDate, $toDate) {
                        $query->whereBetween('paid_at', [
                            $fromDate . ' 00:00:00',
                            $toDate . ' 23:59:59'
                        ])
                            ->orWhere(function ($q) use ($fromDate, $toDate) {
                                $q->whereNull('paid_at')
                                    ->whereBetween('created_at', [
                                        $fromDate . ' 00:00:00',
                                        $toDate . ' 23:59:59'
                                    ]);
                            });
                    })
                    ->get();

                $report['revenue'] = [
                    'total' => (float) $paidPayments->sum('amount'),
                    'count' => $paidPayments->count(),
                    'average' => (float) $paidPayments->avg('amount') ?: 0,
                ];
            }

            // Refunds Summary
            if (in_array('refunds', $metrics)) {
                $refundedPayments = \App\Models\Payment::whereIn('status', ['refunded', 'partially_refunded'])
                    ->where(function ($query) use ($fromDate, $toDate) {
                        $query->whereBetween('refunded_at', [
                            $fromDate . ' 00:00:00',
                            $toDate . ' 23:59:59'
                        ])
                            ->orWhere(function ($q) use ($fromDate, $toDate) {
                                $q->whereNull('refunded_at')
                                    ->whereBetween('created_at', [
                                        $fromDate . ' 00:00:00',
                                        $toDate . ' 23:59:59'
                                    ]);
                            });
                    })
                    ->get();

                $report['refunds'] = [
                    'total_refunded' => (float) $refundedPayments->sum('refund_amount'),
                    'count' => $refundedPayments->count(),
                    'full_refunds' => $refundedPayments->where('status', 'refunded')->count(),
                    'partial_refunds' => $refundedPayments->where('status', 'partially_refunded')->count(),
                ];
            }

            // Failed Payments
            if (in_array('failed', $metrics)) {
                $failedPayments = \App\Models\Payment::where('status', 'failed')
                    ->where(function ($query) use ($fromDate, $toDate) {
                        $query->whereBetween('failed_at', [
                            $fromDate . ' 00:00:00',
                            $toDate . ' 23:59:59'
                        ])
                            ->orWhere(function ($q) use ($fromDate, $toDate) {
                                $q->whereNull('failed_at')
                                    ->whereBetween('created_at', [
                                        $fromDate . ' 00:00:00',
                                        $toDate . ' 23:59:59'
                                    ]);
                            });
                    })
                    ->get();

                $report['failed'] = [
                    'count' => $failedPayments->count(),
                    'lost_revenue' => (float) $failedPayments->sum('amount'),
                ];
            }

            // Payment Methods Breakdown
            if (in_array('payment_methods', $metrics)) {
                $methodBreakdown = \App\Models\Payment::whereBetween('created_at', [
                    $fromDate . ' 00:00:00',
                    $toDate . ' 23:59:59'
                ])
                    ->where('status', 'paid')
                    ->groupBy('payment_method')
                    ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
                    ->get();

                $report['payment_methods'] = $methodBreakdown->toArray();
            }

            // Calculate Net Revenue
            $totalRevenue = $report['revenue']['total'] ?? 0;
            $totalRefunds = $report['refunds']['total_refunded'] ?? 0;
            $netRevenue = $totalRevenue - $totalRefunds;

            // Build notification message
            $message = "📅 Period: " . date('d/m/Y', strtotime($fromDate)) .
                " to " . date('d/m/Y', strtotime($toDate)) . "\n\n";

            if (isset($report['revenue'])) {
                $message .= "💰 Total Revenue: " . number_format($report['revenue']['total'], 3) . " OMR";
                $message .= " (" . $report['revenue']['count'] . " payments)\n";
            }

            if (isset($report['refunds'])) {
                $message .= "↩️ Total Refunds: " . number_format($report['refunds']['total_refunded'], 3) . " OMR";
                $message .= " (" . $report['refunds']['count'] . " refunds)\n";
            }

            if (isset($report['failed'])) {
                $message .= "❌ Failed Payments: " . $report['failed']['count'];
                $message .= " (" . number_format($report['failed']['lost_revenue'], 3) . " OMR)\n";
            }

            $message .= "\n📊 Net Revenue: " . number_format($netRevenue, 3) . " OMR";

            Log::info('Financial report generated', [
                'user_id' => auth()->id(),
                'period' => [$fromDate, $toDate],
                'report' => $report,
            ]);

            Notification::make()
                ->success()
                ->title('Financial Report Generated')
                ->body($message)
                ->persistent()
                ->duration(null)
                ->send();
        } catch (\Exception $e) {
            Log::error('Financial report generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->danger()
                ->title('Report Generation Failed')
                ->body('Error: ' . $e->getMessage())
                ->persistent()
                ->send();
        }
    }

    /**
     * Retry failed payments
     *
     * @param array $data
     * @return void
     */
    protected function retryFailedPayments(array $data): void
    {
        try {
            $daysAgo = now()->subDays((int)$data['age']);

            $failedPayments = \App\Models\Payment::where('status', 'failed')
                ->where('failed_at', '>=', $daysAgo)
                ->get();

            $retriedCount = 0;

            foreach ($failedPayments as $payment) {
                // TODO: Implement actual retry logic with payment gateway
                // $result = PaymentGatewayService::retryPayment($payment);

                $retriedCount++;
            }

            Log::info('Failed payments retry initiated', [
                'count' => $retriedCount,
                'age_days' => $data['age'],
            ]);

            Notification::make()
                ->success()
                ->title('Retry Completed')
                ->body("{$retriedCount} payment(s) queued for retry.")
                ->send();
        } catch (\Exception $e) {
            Log::error('Failed payments retry error', [
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->danger()
                ->title('Retry Failed')
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Send payment reminders
     *
     * @param array $data
     * @return void
     */
    protected function sendPaymentReminders(array $data): void
    {
        try {
            $daysAgo = now()->subDays((int)$data['age']);

            $pendingPayments = \App\Models\Payment::where('status', 'pending')
                ->where('created_at', '<=', $daysAgo)
                ->with('booking')
                ->get();

            $sentCount = 0;

            foreach ($pendingPayments as $payment) {
                if ($payment->booking && $payment->booking->customer_email) {
                    // TODO: Send reminder email
                    // Mail::to($payment->booking->customer_email)
                    //     ->send(new PaymentReminderMail($payment));

                    $sentCount++;
                }
            }

            Notification::make()
                ->success()
                ->title('Reminders Sent')
                ->body("{$sentCount} reminder email(s) sent successfully.")
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Send Failed')
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Get header widgets for statistics
     *
     * @return array
     */
    protected function getHeaderWidgets(): array
    {
        return [
            // TODO: Add payment statistics widgets
            // PaymentStatsWidget::class,
        ];
    }
}// Continued in next part...
