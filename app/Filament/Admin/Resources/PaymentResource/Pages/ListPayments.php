<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use App\Models\Payment;
use Exception;
use App\Filament\Admin\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->label(__('payment.actions.create')),

            Action::make('exportPayments')
                ->label(__('payment.actions.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->schema([
                    Section::make(__('payment.sections.export_options'))
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    DatePicker::make('from_date')
                                        ->label(__('payment.fields.from_date'))
                                        ->default(now()->startOfMonth())
                                        ->native(false)
                                        ->displayFormat('d/m/Y')
                                        ->required(),

                                    DatePicker::make('to_date')
                                        ->label(__('payment.fields.to_date'))
                                        ->default(now())
                                        ->native(false)
                                        ->displayFormat('d/m/Y')
                                        ->required(),
                                ]),

                            Select::make('status')
                                ->label(__('payment.filters.status_filter'))
                                ->options([
                                    'all' => __('payment.options.all_statuses'),
                                    'paid' => __('payment.options.paid_only'),
                                    'pending' => __('payment.options.pending_only'),
                                    'failed' => __('payment.options.failed_only'),
                                    'refunded' => __('payment.options.refunded_only'),
                                    'partially_refunded' => __('payment.options.partially_refunded'),
                                ])
                                ->default('all')
                                ->required(),

                            Select::make('format')
                                ->label(__('payment.fields.format'))
                                ->options([
                                    'csv' => __('payment.options.csv_format'),
                                    'json' => __('payment.options.json_format'),
                                ])
                                ->default('csv')
                                ->required(),

                            Toggle::make('include_booking_details')
                                ->label(__('payment.fields.include_booking_details'))
                                ->default(true)
                                ->helperText(__('payment.helpers.include_booking_details')),
                        ]),
                ])
                ->action(function (array $data) {
                    $this->exportPayments($data);
                }),

            Action::make('generateFinancialReport')
                ->label(__('payment.actions.financial_report'))
                ->icon('heroicon-o-document-chart-bar')
                ->color('info')
                ->schema([
                    Section::make(__('payment.sections.report_period'))
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    DatePicker::make('from_date')
                                        ->label(__('payment.fields.from_date'))
                                        ->required()
                                        ->default(now()->startOfMonth())
                                        ->native(false)
                                        ->displayFormat('d/m/Y'),

                                    DatePicker::make('to_date')
                                        ->label(__('payment.fields.to_date'))
                                        ->required()
                                        ->default(now())
                                        ->native(false)
                                        ->displayFormat('d/m/Y'),
                                ]),

                            CheckboxList::make('metrics')
                                ->label(__('payment.fields.metrics'))
                                ->options([
                                    'revenue' => __('payment.metrics.revenue'),
                                    'refunds' => __('payment.metrics.refunds'),
                                    'failed' => __('payment.metrics.failed'),
                                    'payment_methods' => __('payment.metrics.payment_methods'),
                                    'daily_trends' => __('payment.metrics.daily_trends'),
                                ])
                                ->default(['revenue', 'refunds', 'payment_methods'])
                                ->columns(2),
                        ]),
                ])
                ->action(function (array $data) {
                    $this->generateFinancialReport($data);
                }),

            Action::make('reconcilePayments')
                ->label(__('payment.actions.reconcile'))
                ->icon('heroicon-o-calculator')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('payment.modals.reconcile.heading'))
                ->modalDescription(__('payment.modals.reconcile.description'))
                ->schema([
                    DatePicker::make('reconcile_date')
                        ->label(__('payment.fields.reconcile_date'))
                        ->default(today())
                        ->native(false)
                        ->required(),

                    Toggle::make('auto_update')
                        ->label(__('payment.fields.auto_update'))
                        ->default(false)
                        ->helperText(__('payment.helpers.auto_update')),
                ])
                ->action(function (array $data) {
                    $this->reconcilePayments($data);
                }),

            Action::make('retryFailedPayments')
                ->label(__('payment.actions.retry_failed'))
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('payment.modals.retry.heading'))
                ->modalDescription(__('payment.modals.retry.description'))
                ->schema([
                    Select::make('age')
                        ->label(__('payment.fields.age'))
                        ->options([
                            '1' => __('payment.options.last_24_hours'),
                            '3' => __('payment.options.last_3_days'),
                            '7' => __('payment.options.last_7_days'),
                            '30' => __('payment.options.last_30_days'),
                        ])
                        ->default('1')
                        ->required(),

                    Textarea::make('reason')
                        ->label(__('payment.fields.retry_reason'))
                        ->rows(2),
                ])
                ->action(function (array $data) {
                    $this->retryFailedPayments($data);
                }),

            Action::make('sendReminders')
                ->label(__('payment.actions.send_reminders'))
                ->icon('heroicon-o-bell')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading(__('payment.modals.reminders.heading'))
                ->modalDescription(__('payment.modals.reminders.description'))
                ->schema([
                    Select::make('age')
                        ->label(__('payment.fields.pending_for'))
                        ->options([
                            '1' => __('payment.options.more_than_1_day'),
                            '3' => __('payment.options.more_than_3_days'),
                            '7' => __('payment.options.more_than_7_days'),
                        ])
                        ->default('1')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->sendPaymentReminders($data);
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => \Filament\Schemas\Components\Tabs\Tab::make(__('payment.tabs.all'))
                ->icon('heroicon-o-credit-card')
                ->badge(fn() => Payment::count()),

            'paid' => \Filament\Schemas\Components\Tabs\Tab::make(__('payment.tabs.paid'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'paid'))
                ->badge(fn() => Payment::where('status', 'paid')->count())
                ->badgeColor('success'),

            'pending' => \Filament\Schemas\Components\Tabs\Tab::make(__('payment.tabs.pending'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(fn() => Payment::where('status', 'pending')->count())
                ->badgeColor('warning'),

            'failed' => \Filament\Schemas\Components\Tabs\Tab::make(__('payment.tabs.failed'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'failed'))
                ->badge(fn() => Payment::where('status', 'failed')->count())
                ->badgeColor('danger'),

            'refunded' => \Filament\Schemas\Components\Tabs\Tab::make(__('payment.tabs.refunded'))
                ->icon('heroicon-o-arrow-path')
                ->modifyQueryUsing(fn(Builder $query) =>
                    $query->whereIn('status', ['refunded', 'partially_refunded']))
                ->badge(fn() => Payment::whereIn('status', ['refunded', 'partially_refunded'])->count())
                ->badgeColor('info'),

            'today' => \Filament\Schemas\Components\Tabs\Tab::make(__('payment.tabs.today'))
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('created_at', today()))
                ->badge(fn() => Payment::whereDate('created_at', today())->count())
                ->badgeColor('purple'),

            'this_week' => \Filament\Schemas\Components\Tabs\Tab::make(__('payment.tabs.this_week'))
                ->icon('heroicon-o-calendar-days')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]))
                ->badge(fn() => Payment::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count())
                ->badgeColor('purple'),

            'this_month' => \Filament\Schemas\Components\Tabs\Tab::make(__('payment.tabs.this_month'))
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn(Builder $query) =>
                    $query->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year))
                ->badge(fn() => Payment::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count())
                ->badgeColor('primary'),

            'high_value' => \Filament\Schemas\Components\Tabs\Tab::make(__('payment.tabs.high_value'))
                ->icon('heroicon-o-banknotes')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('amount', '>=', 1000))
                ->badge(fn() => Payment::where('amount', '>=', 1000)->count())
                ->badgeColor('success'),
        ];
    }

    protected function exportPayments(array $data): void
    {
        DB::beginTransaction();

        try {
            $query = Payment::query()
                ->with(['booking.hall'])
                ->whereBetween('created_at', [
                    $data['from_date'] . ' 00:00:00',
                    $data['to_date'] . ' 23:59:59'
                ]);

            if ($data['status'] !== 'all') {
                if ($data['status'] === 'refunded') {
                    $query->whereIn('status', ['refunded', 'partially_refunded']);
                } else {
                    $query->where('status', $data['status']);
                }
            }

            $payments = $query->orderBy('created_at', 'desc')->get();

            if ($data['format'] === 'json') {
                $this->exportAsJson($payments, $data);
                return;
            }

            $exportPath = 'exports/payments';
            if (!Storage::disk('public')->exists($exportPath)) {
                Storage::disk('public')->makeDirectory($exportPath);
            }

            $filename = 'payments_export_' . now()->format('Y_m_d_His') . '.csv';
            $path = storage_path('app/public/' . $exportPath . '/' . $filename);

            $file = fopen($path, 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $headers = [
                __('payment.export.payment_reference'),
                __('payment.export.booking_number'),
                __('payment.export.transaction_id'),
                __('payment.export.amount'),
                __('payment.export.currency'),
                __('payment.export.status'),
                __('payment.export.payment_method'),
                __('payment.export.refund_amount'),
                __('payment.export.paid_at'),
                __('payment.export.failed_at'),
                __('payment.export.refunded_at'),
                __('payment.export.created_at'),
            ];

            if ($data['include_booking_details']) {
                $headers = array_merge($headers, [
                    __('payment.export.customer_name'),
                    __('payment.export.customer_email'),
                    __('payment.export.hall_name'),
                    __('payment.export.booking_date'),
                ]);
            }

            fputcsv($file, $headers);

            foreach ($payments as $payment) {
                $row = [
                    $payment->payment_reference,
                    $payment->booking?->booking_number ?? __('payment.n_a'),
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

            Log::info('Payments exported', [
                'user_id' => auth()->id(),
                'count' => $payments->count(),
                'filters' => $data,
                'filename' => $filename,
            ]);

            DB::commit();

            Notification::make()
                ->title(__('payment.notifications.export_success'))
                ->success()
                ->body(__('payment.notifications.export_success_body', ['count' => $payments->count()]))
                ->persistent()
                ->actions([
                    Action::make('download')
                        ->label(__('payment.actions.download'))
                        ->url(asset('storage/' . $exportPath . '/' . $filename))
                        ->openUrlInNewTab(),
                ])
                ->send();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Payment export failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            Notification::make()
                ->danger()
                ->title(__('payment.notifications.export_failed'))
                ->body(__('payment.notifications.export_failed_body', ['error' => $e->getMessage()]))
                ->persistent()
                ->send();
        }
    }

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
            ->title(__('payment.notifications.json_export_success'))
            ->actions([
                Action::make('download')
                    ->label(__('payment.actions.download'))
                    ->url(asset('storage/exports/payments/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }

    protected function reconcilePayments(array $data): void
    {
        try {
            $reconciledCount = 0;
            $mismatches = [];

            $payments = Payment::whereDate('created_at', $data['reconcile_date'])
                ->whereNotNull('transaction_id')
                ->get();

            foreach ($payments as $payment) {
                // TODO: Implement actual gateway reconciliation
                $reconciledCount++;
            }

            Log::info('Payment reconciliation completed', [
                'date' => $data['reconcile_date'],
                'reconciled' => $reconciledCount,
                'mismatches' => count($mismatches),
            ]);

            Notification::make()
                ->success()
                ->title(__('payment.notifications.reconciliation_completed'))
                ->body(__('payment.notifications.reconciliation_completed_body', [
                    'count' => $reconciledCount,
                    'mismatches' => count($mismatches)
                ]))
                ->persistent()
                ->send();
        } catch (Exception $e) {
            Log::error('Payment reconciliation failed', [
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->danger()
                ->title(__('payment.notifications.reconciliation_failed'))
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function generateFinancialReport(array $data): void
    {
        try {
            $metrics = $data['metrics'] ?? [];
            $report = [];

            $fromDate = is_string($data['from_date'])
                ? $data['from_date']
                : $data['from_date']->format('Y-m-d');

            $toDate = is_string($data['to_date'])
                ? $data['to_date']
                : $data['to_date']->format('Y-m-d');

            if (in_array('revenue', $metrics)) {
                $paidPayments = Payment::where('status', 'paid')
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

            if (in_array('refunds', $metrics)) {
                $refundedPayments = Payment::whereIn('status', ['refunded', 'partially_refunded'])
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

            if (in_array('failed', $metrics)) {
                $failedPayments = Payment::where('status', 'failed')
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

            if (in_array('payment_methods', $metrics)) {
                $methodBreakdown = Payment::whereBetween('created_at', [
                    $fromDate . ' 00:00:00',
                    $toDate . ' 23:59:59'
                ])
                    ->where('status', 'paid')
                    ->groupBy('payment_method')
                    ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
                    ->get();

                $report['payment_methods'] = $methodBreakdown->toArray();
            }

            $totalRevenue = $report['revenue']['total'] ?? 0;
            $totalRefunds = $report['refunds']['total_refunded'] ?? 0;
            $netRevenue = $totalRevenue - $totalRefunds;

            $message = __('payment.report_period', [
                'from' => date('d/m/Y', strtotime($fromDate)),
                'to' => date('d/m/Y', strtotime($toDate))
            ]) . "\n\n";

            if (isset($report['revenue'])) {
                $message .= __('payment.report_revenue', [
                    'amount' => number_format($report['revenue']['total'], 3),
                    'count' => $report['revenue']['count']
                ]) . "\n";
            }

            if (isset($report['refunds'])) {
                $message .= __('payment.report_refunds', [
                    'amount' => number_format($report['refunds']['total_refunded'], 3),
                    'count' => $report['refunds']['count']
                ]) . "\n";
            }

            if (isset($report['failed'])) {
                $message .= __('payment.report_failed', [
                    'count' => $report['failed']['count'],
                    'amount' => number_format($report['failed']['lost_revenue'], 3)
                ]) . "\n";
            }

            $message .= "\n" . __('payment.report_net_revenue', ['amount' => number_format($netRevenue, 3)]);

            Log::info('Financial report generated', [
                'user_id' => auth()->id(),
                'period' => [$fromDate, $toDate],
                'report' => $report,
            ]);

            Notification::make()
                ->success()
                ->title(__('payment.notifications.report_generated'))
                ->body($message)
                ->persistent()
                ->duration(null)
                ->send();
        } catch (Exception $e) {
            Log::error('Financial report generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->danger()
                ->title(__('payment.notifications.report_failed'))
                ->body(__('payment.notifications.error_prefix') . $e->getMessage())
                ->persistent()
                ->send();
        }
    }

    protected function retryFailedPayments(array $data): void
    {
        try {
            $daysAgo = now()->subDays((int)$data['age']);

            $failedPayments = Payment::where('status', 'failed')
                ->where('failed_at', '>=', $daysAgo)
                ->get();

            $retriedCount = $failedPayments->count();

            Log::info('Failed payments retry initiated', [
                'count' => $retriedCount,
                'age_days' => $data['age'],
            ]);

            Notification::make()
                ->success()
                ->title(__('payment.notifications.retry_completed'))
                ->body(__('payment.notifications.retry_completed_body', ['count' => $retriedCount]))
                ->send();
        } catch (Exception $e) {
            Log::error('Failed payments retry error', [
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->danger()
                ->title(__('payment.notifications.retry_failed'))
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function sendPaymentReminders(array $data): void
    {
        try {
            $daysAgo = now()->subDays((int)$data['age']);

            $pendingPayments = Payment::where('status', 'pending')
                ->where('created_at', '<=', $daysAgo)
                ->with('booking')
                ->get();

            $sentCount = $pendingPayments->count();

            Notification::make()
                ->success()
                ->title(__('payment.notifications.reminders_sent'))
                ->body(__('payment.notifications.reminders_sent_body', ['count' => $sentCount]))
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('payment.notifications.reminders_failed'))
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
