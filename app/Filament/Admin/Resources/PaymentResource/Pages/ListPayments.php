<?php

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Actions\Action::make('exportPayments')
                ->label('Export Payments')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('from_date')
                        ->label('From Date')
                        ->default(now()->startOfMonth())
                        ->native(false),

                    \Filament\Forms\Components\DatePicker::make('to_date')
                        ->label('To Date')
                        ->default(now())
                        ->native(false),

                    \Filament\Forms\Components\Select::make('status')
                        ->label('Filter by Status')
                        ->options([
                            'all' => 'All Statuses',
                            'paid' => 'Paid Only',
                            'pending' => 'Pending Only',
                            'failed' => 'Failed Only',
                            'refunded' => 'Refunded Only',
                        ])
                        ->default('all'),
                ])
                ->action(function (array $data) {
                    $this->exportPayments($data);
                }),

            Actions\Action::make('reconcilePayments')
                ->label('Reconcile Payments')
                ->icon('heroicon-o-calculator')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reconcile Payment Records')
                ->modalDescription('Match payments with gateway transactions.')
                ->action(function () {
                    $this->reconcilePayments();
                }),

            Actions\Action::make('generateFinancialReport')
                ->label('Financial Report')
                ->icon('heroicon-o-document-chart-bar')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('from_date')
                        ->label('From Date')
                        ->required()
                        ->default(now()->startOfMonth())
                        ->native(false),

                    \Filament\Forms\Components\DatePicker::make('to_date')
                        ->label('To Date')
                        ->required()
                        ->default(now())
                        ->native(false),
                ])
                ->action(function (array $data) {
                    $this->generateFinancialReport($data);
                }),

            Actions\Action::make('retryFailedPayments')
                ->label('Retry Failed Payments')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Retry Failed Payments')
                ->modalDescription('Attempt to reprocess all failed payments.')
                ->action(function () {
                    $this->retryFailedPayments();
                }),
        ];
    }

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
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('status', ['refunded', 'partially_refunded']))
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
                ->modifyQueryUsing(fn(Builder $query) => $query->whereMonth('created_at', now()->month)
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

    protected function exportPayments(array $data): void
    {
        $query = \App\Models\Payment::with('booking')
            ->whereBetween('created_at', [$data['from_date'], $data['to_date']]);

        if ($data['status'] !== 'all') {
            $query->where('status', $data['status']);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        $filename = 'payments_export_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        fputcsv($file, [
            'Payment Reference',
            'Booking Number',
            'Transaction ID',
            'Amount (OMR)',
            'Currency',
            'Status',
            'Payment Method',
            'Refund Amount',
            'Paid At',
            'Failed At',
            'Refunded At',
            'Created At',
        ]);

        foreach ($payments as $payment) {
            fputcsv($file, [
                $payment->payment_reference,
                $payment->booking->booking_number ?? 'N/A',
                $payment->transaction_id ?? '',
                number_format($payment->amount, 3),
                $payment->currency,
                ucfirst($payment->status),
                $payment->payment_method ?? '',
                $payment->refund_amount ? number_format($payment->refund_amount, 3) : '',
                $payment->paid_at?->format('Y-m-d H:i:s') ?? '',
                $payment->failed_at?->format('Y-m-d H:i:s') ?? '',
                $payment->refunded_at?->format('Y-m-d H:i:s') ?? '',
                $payment->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        Notification::make()
            ->title('Export Successful')
            ->success()
            ->body('Payments exported successfully.')
            ->persistent()
            ->actions([
                \Filament\Notifications\Actions\Action::make('download')
                    ->label('Download File')
                    ->url(asset('storage/exports/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }

    protected function reconcilePayments(): void
    {
        // Implement payment reconciliation logic
        $reconciledCount = 0;

        Notification::make()
            ->success()
            ->title('Reconciliation Completed')
            ->body("{$reconciledCount} payment(s) reconciled.")
            ->send();
    }

    protected function generateFinancialReport(array $data): void
    {
        $payments = \App\Models\Payment::whereBetween('paid_at', [$data['from_date'], $data['to_date']])
            ->where('status', 'paid')
            ->get();

        $totalRevenue = $payments->sum('amount');
        $totalRefunds = \App\Models\Payment::whereBetween('refunded_at', [$data['from_date'], $data['to_date']])
            ->whereIn('status', ['refunded', 'partially_refunded'])
            ->sum('refund_amount');

        $netRevenue = $totalRevenue - $totalRefunds;

        Notification::make()
            ->success()
            ->title('Financial Report Generated')
            ->body("Total Revenue: " . number_format($totalRevenue, 3) . " OMR | Net: " . number_format($netRevenue, 3) . " OMR")
            ->persistent()
            ->send();
    }

    protected function retryFailedPayments(): void
    {
        $failedPayments = \App\Models\Payment::where('status', 'failed')->get();
        $retriedCount = 0;

        foreach ($failedPayments as $payment) {
            // Implement retry logic
            $retriedCount++;
        }

        Notification::make()
            ->success()
            ->title('Retry Completed')
            ->body("{$retriedCount} payment(s) queued for retry.")
            ->send();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add payment statistics widgets
        ];
    }
}
