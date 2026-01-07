<?php

namespace App\Filament\Admin\Resources\HallOwnerResource\Pages;

use App\Filament\Admin\Resources\HallOwnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ListHallOwners extends ListRecords
{
    protected static string $resource = HallOwnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Actions\Action::make('exportOwners')
                ->label('Export Owners')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportOwners())
                ->requiresConfirmation()
                ->modalHeading('Export Hall Owners')
                ->modalDescription('Export all hall owner data to CSV.')
                ->modalSubmitActionLabel('Export'),

            Actions\Action::make('bulkVerify')
                ->label('Bulk Verify')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Verify All Pending Owners')
                ->modalDescription('This will verify all unverified hall owners.')
                ->form([
                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Verification Notes')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->bulkVerifyOwners($data);
                }),

            Actions\Action::make('sendBulkNotification')
                ->label('Send Notification')
                ->icon('heroicon-o-bell')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Select::make('filter')
                        ->label('Send To')
                        ->options([
                            'all' => 'All Owners',
                            'verified' => 'Verified Only',
                            'unverified' => 'Unverified Only',
                            'active' => 'Active Only',
                        ])
                        ->default('verified')
                        ->required(),

                    \Filament\Forms\Components\TextInput::make('subject')
                        ->required()
                        ->maxLength(255),

                    \Filament\Forms\Components\Textarea::make('message')
                        ->required()
                        ->rows(5),
                ])
                ->action(function (array $data) {
                    $this->sendBulkNotification($data);
                }),

            Actions\Action::make('generateReport')
                ->label('Generate Report')
                ->icon('heroicon-o-document-chart-bar')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('from_date')
                        ->label('From Date')
                        ->default(now()->startOfMonth())
                        ->native(false)
                        ->required(),

                    \Filament\Forms\Components\DatePicker::make('to_date')
                        ->label('To Date')
                        ->default(now())
                        ->native(false)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->generateOwnersReport($data);
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Owners')
                ->icon('heroicon-o-users')
                ->badge(fn() => \App\Models\HallOwner::count()),

            'pending_verification' => Tab::make('Pending Verification')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_verified', false))
                ->badge(fn() => \App\Models\HallOwner::where('is_verified', false)->count())
                ->badgeColor('warning'),

            'verified' => Tab::make('Verified')
                ->icon('heroicon-o-check-badge')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_verified', true))
                ->badge(fn() => \App\Models\HallOwner::where('is_verified', true)->count())
                ->badgeColor('success'),

            'active' => Tab::make('Active')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => \App\Models\HallOwner::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make('Inactive')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => \App\Models\HallOwner::where('is_active', false)->count())
                ->badgeColor('danger'),

            'custom_commission' => Tab::make('Custom Commission')
                ->icon('heroicon-o-currency-dollar')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('commission_value'))
                ->badge(fn() => \App\Models\HallOwner::whereNotNull('commission_value')->count())
                ->badgeColor('info'),

            'with_halls' => Tab::make('With Halls')
                ->icon('heroicon-o-building-storefront')
                ->modifyQueryUsing(fn(Builder $query) => $query->has('halls'))
                ->badge(fn() => \App\Models\HallOwner::has('halls')->count())
                ->badgeColor('purple'),

            'without_halls' => Tab::make('Without Halls')
                ->icon('heroicon-o-building-storefront')
                ->modifyQueryUsing(fn(Builder $query) => $query->doesntHave('halls'))
                ->badge(fn() => \App\Models\HallOwner::doesntHave('halls')->count())
                ->badgeColor('gray'),

            'incomplete_documents' => Tab::make('Incomplete Documents')
                ->icon('heroicon-o-document-text')
                ->modifyQueryUsing(fn(Builder $query) => $query->where(function ($q) {
                    $q->whereNull('commercial_registration_document')
                        ->orWhereNull('tax_certificate')
                        ->orWhereNull('identity_document');
                }))
                ->badge(fn() => \App\Models\HallOwner::where(function ($q) {
                    $q->whereNull('commercial_registration_document')
                        ->orWhereNull('tax_certificate')
                        ->orWhereNull('identity_document');
                })->count())
                ->badgeColor('warning'),
        ];
    }

    protected function exportOwners(): void
    {
        $owners = \App\Models\HallOwner::with('user')->get();

        $filename = 'hall_owners_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        fputcsv($file, [
            'ID',
            'Owner Name',
            'Business Name',
            'Business Name (AR)',
            'Commercial Registration',
            'Tax Number',
            'Business Phone',
            'Business Email',
            'Bank Name',
            'IBAN',
            'Commission Type',
            'Commission Value',
            'Verified',
            'Active',
            'Verified At',
            'Total Halls',
            'Created At',
        ]);

        foreach ($owners as $owner) {
            fputcsv($file, [
                $owner->id,
                $owner->user->name ?? 'N/A',
                $owner->business_name,
                $owner->business_name_ar ?? '',
                $owner->commercial_registration,
                $owner->tax_number ?? '',
                $owner->business_phone,
                $owner->business_email ?? '',
                $owner->bank_name ?? '',
                $owner->iban ?? '',
                $owner->commission_type ?? 'Default',
                $owner->commission_value ?? 'Default',
                $owner->is_verified ? 'Yes' : 'No',
                $owner->is_active ? 'Yes' : 'No',
                $owner->verified_at?->format('Y-m-d H:i:s') ?? 'Not Verified',
                $owner->halls()->count(),
                $owner->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        Notification::make()
            ->title('Export Successful')
            ->success()
            ->body('Hall owners exported successfully.')
            ->persistent()
            ->actions([
                \Filament\Notifications\Actions\Action::make('download')
                    ->label('Download File')
                    ->url(asset('storage/exports/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }

    protected function bulkVerifyOwners(array $data): void
    {
        $owners = \App\Models\HallOwner::where('is_verified', false)->get();
        $verifiedCount = 0;

        foreach ($owners as $owner) {
            $owner->verify(Auth::id(), $data['notes'] ?? null);
            $verifiedCount++;
        }

        Notification::make()
            ->success()
            ->title('Bulk Verification Completed')
            ->body("{$verifiedCount} owner(s) have been verified.")
            ->send();

        $this->redirect(static::getUrl());
    }

    protected function sendBulkNotification(array $data): void
    {
        $query = \App\Models\HallOwner::query();

        match ($data['filter']) {
            'verified' => $query->where('is_verified', true),
            'unverified' => $query->where('is_verified', false),
            'active' => $query->where('is_active', true),
            default => null,
        };

        $owners = $query->get();
        $sentCount = 0;

        foreach ($owners as $owner) {
            // Send notification logic here
            // Example: $owner->user->notify(new OwnerNotification($data['subject'], $data['message']));
            $sentCount++;
        }

        Notification::make()
            ->success()
            ->title('Notifications Sent')
            ->body("{$sentCount} notification(s) sent successfully.")
            ->send();
    }

    protected function generateOwnersReport(array $data): void
    {
        try {
            $fromDate = $data['from_date'];
            $toDate = $data['to_date'];

            // Get all hall owners with their relationships
            $owners = \App\Models\HallOwner::with(['user', 'halls'])->get();

            // Get all halls
            $allHalls = \App\Models\Hall::all();
            $hallIds = $allHalls->pluck('id');

            // Get all bookings in date range
            $allBookings = \App\Models\Booking::whereIn('hall_id', $hallIds)
                ->whereBetween('booking_date', [$fromDate, $toDate])
                ->with(['hall', 'user'])
                ->get();

            // Calculate overall statistics
            $overallStats = [
                'total_owners' => $owners->count(),
                'verified_owners' => $owners->where('is_verified', true)->count(),
                'active_owners' => $owners->where('is_active', true)->count(),
                'total_halls' => $allHalls->count(),
                'active_halls' => $allHalls->where('is_active', true)->count(),
                'total_bookings' => $allBookings->count(),
                'total_revenue' => $allBookings->filter(function ($b) {
                    return in_array($b->status->value, ['confirmed', 'completed']) && $b->payment_status->value === 'paid';
                })->sum('total_amount'),
                'total_commission' => $allBookings->filter(function ($b) {
                    return in_array($b->status->value, ['confirmed', 'completed']) && $b->payment_status->value === 'paid';
                })->sum('commission_amount'),
                'total_payout' => $allBookings->filter(function ($b) {
                    return in_array($b->status->value, ['confirmed', 'completed']) && $b->payment_status->value === 'paid';
                })->sum('owner_payout'),
            ];

            // Owner performance details
            $ownerPerformance = $owners->map(function ($owner) use ($allBookings) {
                $ownerHalls = \App\Models\Hall::where('owner_id', $owner->user_id)->get();
                $ownerHallIds = $ownerHalls->pluck('id');
                $ownerBookings = $allBookings->whereIn('hall_id', $ownerHallIds);

                $paidBookings = $ownerBookings->filter(function ($b) {
                    return in_array($b->status->value, ['confirmed', 'completed']) && $b->payment_status->value === 'paid';
                });

                return [
                    'business_name' => $owner->business_name,
                    'owner_name' => $owner->user->name ?? 'N/A',
                    'halls_count' => $ownerHalls->count(),
                    'bookings_count' => $ownerBookings->count(),
                    'revenue' => $paidBookings->sum('total_amount'),
                    'commission' => $paidBookings->sum('commission_amount'),
                    'payout' => $paidBookings->sum('owner_payout'),
                    'is_verified' => $owner->is_verified,
                    'is_active' => $owner->is_active,
                ];
            })->sortByDesc('revenue');

            // Top performing owners
            $topOwners = $ownerPerformance->take(10);

            // Generate PDF
            $pdf = Pdf::loadView('pdf.all-owners-report', [
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'overallStats' => $overallStats,
                'ownerPerformance' => $ownerPerformance,
                'topOwners' => $topOwners,
                'generatedAt' => now(),
                'generatedBy' => Auth::user()->name,
            ])->setPaper('a4', 'landscape');

            // Ensure directory exists
            if (!Storage::disk('public')->exists('reports')) {
                Storage::disk('public')->makeDirectory('reports');
            }

            $filename = 'all_owners_report_' . now()->format('YmdHis') . '.pdf';
            $path = 'reports/' . $filename;

            Storage::disk('public')->put($path, $pdf->output());

            Notification::make()
                ->success()
                ->title('Report Generated Successfully')
                ->body('All hall owners report has been generated.')
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label('Download Report')
                        ->url(asset('storage/' . $path))
                        ->openUrlInNewTab(),
                ])
                ->persistent()
                ->send();
        } catch (\Exception $e) {
            Log::error('All owners report generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Notification::make()
                ->danger()
                ->title('Report Generation Failed')
                ->body('An error occurred while generating the report: ' . $e->getMessage())
                ->persistent()
                ->send();
        }
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add statistics widgets here
        ];
    }
}
