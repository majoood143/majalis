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
use Illuminate\Support\Facades\DB;

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
                ->label(__('hall-owner.actions.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportOwners())
                ->requiresConfirmation()
                ->modalHeading(__('hall-owner.actions.export_modal_heading'))
                ->modalDescription(__('hall-owner.actions.export_modal_description'))
                ->modalSubmitActionLabel(__('hall-owner.actions.export')),

            Actions\Action::make('bulkVerify')
                ->label(__('hall-owner.actions.bulk_verify'))
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('hall-owner.actions.bulk_verify_modal_heading'))
                ->modalDescription(__('hall-owner.actions.bulk_verify_modal_description'))
                ->form([
                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label(__('hall-owner.fields.notes'))
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->bulkVerifyOwners($data);
                }),

            Actions\Action::make('sendBulkNotification')
                ->label(__('hall-owner.actions.send_notification'))
                ->icon('heroicon-o-bell')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Select::make('filter')
                        ->label(__('hall-owner.fields.filter'))
                        ->options([
                            'all' => __('hall-owner.options.all'),
                            'verified' => __('hall-owner.options.verified'),
                            'unverified' => __('hall-owner.options.unverified'),
                            'active' => __('hall-owner.options.active'),
                        ])
                        ->default('verified')
                        ->required(),

                    \Filament\Forms\Components\TextInput::make('subject')
                        ->label(__('hall-owner.fields.subject'))
                        ->required()
                        ->maxLength(255),

                    \Filament\Forms\Components\Textarea::make('message')
                        ->label(__('hall-owner.fields.message'))
                        ->required()
                        ->rows(5),
                ])
                ->action(function (array $data) {
                    $this->sendBulkNotification($data);
                }),

            Actions\Action::make('generateReport')
                ->label(__('hall-owner.actions.generate_report'))
                ->icon('heroicon-o-document-chart-bar')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('from_date')
                        ->label(__('hall-owner.fields.from_date'))
                        ->default(now()->startOfMonth())
                        ->native(false)
                        ->required(),

                    \Filament\Forms\Components\DatePicker::make('to_date')
                        ->label(__('hall-owner.fields.to_date'))
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
            'all' => Tab::make(__('hall-owner.tabs.all'))
                ->icon('heroicon-o-users')
                ->badge(fn() => \App\Models\HallOwner::count()),

            'pending_verification' => Tab::make(__('hall-owner.tabs.pending_verification'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_verified', false))
                ->badge(fn() => \App\Models\HallOwner::where('is_verified', false)->count())
                ->badgeColor('warning'),

            'verified' => Tab::make(__('hall-owner.tabs.verified'))
                ->icon('heroicon-o-check-badge')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_verified', true))
                ->badge(fn() => \App\Models\HallOwner::where('is_verified', true)->count())
                ->badgeColor('success'),

            'active' => Tab::make(__('hall-owner.tabs.active'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => \App\Models\HallOwner::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make(__('hall-owner.tabs.inactive'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => \App\Models\HallOwner::where('is_active', false)->count())
                ->badgeColor('danger'),

            'custom_commission' => Tab::make(__('hall-owner.tabs.custom_commission'))
                ->icon('heroicon-o-currency-dollar')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('commission_value'))
                ->badge(fn() => \App\Models\HallOwner::whereNotNull('commission_value')->count())
                ->badgeColor('info'),

            'with_halls' => Tab::make(__('hall-owner.tabs.with_halls'))
                ->icon('heroicon-o-building-storefront')
                ->modifyQueryUsing(fn(Builder $query) => $query->has('halls'))
                ->badge(fn() => \App\Models\HallOwner::has('halls')->count())
                ->badgeColor('purple'),

            'without_halls' => Tab::make(__('hall-owner.tabs.without_halls'))
                ->icon('heroicon-o-building-storefront')
                ->modifyQueryUsing(fn(Builder $query) => $query->doesntHave('halls'))
                ->badge(fn() => \App\Models\HallOwner::doesntHave('halls')->count())
                ->badgeColor('gray'),

            'incomplete_documents' => Tab::make(__('hall-owner.tabs.incomplete_documents'))
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
        DB::beginTransaction();

        try {
            $owners = \App\Models\HallOwner::with('user')->get();

            $filename = 'hall_owners_' . now()->format('Y_m_d_His') . '.csv';
            $path = storage_path('app/public/exports/' . $filename);

            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $file = fopen($path, 'w');

            // Add UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                __('hall-owner.export.id'),
                __('hall-owner.export.owner_name'),
                __('hall-owner.export.business_name'),
                __('hall-owner.export.business_name_ar'),
                __('hall-owner.export.commercial_registration'),
                __('hall-owner.export.tax_number'),
                __('hall-owner.export.business_phone'),
                __('hall-owner.export.business_email'),
                __('hall-owner.export.bank_name'),
                __('hall-owner.export.iban'),
                __('hall-owner.export.commission_type'),
                __('hall-owner.export.commission_value'),
                __('hall-owner.export.verified'),
                __('hall-owner.export.active'),
                __('hall-owner.export.verified_at'),
                __('hall-owner.export.total_halls'),
                __('hall-owner.export.created_at'),
            ]);

            foreach ($owners as $owner) {
                fputcsv($file, [
                    $owner->id,
                    $owner->user->name ?? __('hall-owner.n_a'),
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
                    $owner->is_verified ? __('hall-owner.yes') : __('hall-owner.no'),
                    $owner->is_active ? __('hall-owner.yes') : __('hall-owner.no'),
                    $owner->verified_at?->format('Y-m-d H:i:s') ?? __('hall-owner.not_verified'),
                    $owner->halls()->count(),
                    $owner->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);

            DB::commit();

            Notification::make()
                ->title(__('hall-owner.notifications.export_success'))
                ->success()
                ->body(__('hall-owner.notifications.export_success_body'))
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label(__('hall-owner.actions.download'))
                        ->url(asset('storage/exports/' . $filename))
                        ->openUrlInNewTab(),
                ])
                ->send();

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title(__('hall-owner.notifications.export_error'))
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function bulkVerifyOwners(array $data): void
    {
        DB::beginTransaction();

        try {
            $owners = \App\Models\HallOwner::where('is_verified', false)->get();
            $verifiedCount = 0;

            foreach ($owners as $owner) {
                $owner->verify(Auth::id(), $data['notes'] ?? null);
                $verifiedCount++;
            }

            DB::commit();

            Notification::make()
                ->success()
                ->title(__('hall-owner.notifications.bulk_verify_success'))
                ->body(__('hall-owner.notifications.bulk_verify_success_body', ['count' => $verifiedCount]))
                ->send();

            $this->redirect(static::getUrl());

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title(__('hall-owner.notifications.update_error'))
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function sendBulkNotification(array $data): void
    {
        DB::beginTransaction();

        try {
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

            DB::commit();

            Notification::make()
                ->success()
                ->title(__('hall-owner.notifications.notification_sent'))
                ->body(__('hall-owner.notifications.notification_sent_body', ['count' => $sentCount]))
                ->send();

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title(__('hall-owner.notifications.update_error'))
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function generateOwnersReport(array $data): void
    {
        DB::beginTransaction();

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
                    $status = is_string($b->status) ? $b->status : $b->status->value;
                    $paymentStatus = is_string($b->payment_status) ? $b->payment_status : $b->payment_status->value;
                    return in_array($status, ['confirmed', 'completed']) && $paymentStatus === 'paid';
                })->sum('total_amount'),
                'total_commission' => $allBookings->filter(function ($b) {
                    $status = is_string($b->status) ? $b->status : $b->status->value;
                    $paymentStatus = is_string($b->payment_status) ? $b->payment_status : $b->payment_status->value;
                    return in_array($status, ['confirmed', 'completed']) && $paymentStatus === 'paid';
                })->sum('commission_amount'),
                'total_payout' => $allBookings->filter(function ($b) {
                    $status = is_string($b->status) ? $b->status : $b->status->value;
                    $paymentStatus = is_string($b->payment_status) ? $b->payment_status : $b->payment_status->value;
                    return in_array($status, ['confirmed', 'completed']) && $paymentStatus === 'paid';
                })->sum('owner_payout'),
            ];

            // Owner performance details
            $ownerPerformance = $owners->map(function ($owner) use ($allBookings) {
                $ownerHalls = \App\Models\Hall::where('owner_id', $owner->user_id)->get();
                $ownerHallIds = $ownerHalls->pluck('id');
                $ownerBookings = $allBookings->whereIn('hall_id', $ownerHallIds);

                $paidBookings = $ownerBookings->filter(function ($b) {
                    $status = is_string($b->status) ? $b->status : $b->status->value;
                    $paymentStatus = is_string($b->payment_status) ? $b->payment_status : $b->payment_status->value;
                    return in_array($status, ['confirmed', 'completed']) && $paymentStatus === 'paid';
                });

                return [
                    'business_name' => $owner->business_name,
                    'owner_name' => $owner->user->name ?? __('hall-owner.n_a'),
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

            DB::commit();

            Notification::make()
                ->success()
                ->title(__('hall-owner.notifications.report_generated'))
                ->body(__('hall-owner.notifications.report_generated_body'))
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label(__('hall-owner.actions.download_report'))
                        ->url(asset('storage/' . $path))
                        ->openUrlInNewTab(),
                ])
                ->persistent()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('All owners report generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Notification::make()
                ->danger()
                ->title(__('hall-owner.notifications.report_failed'))
                ->body(__('hall-owner.notifications.update_error') . ': ' . $e->getMessage())
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
