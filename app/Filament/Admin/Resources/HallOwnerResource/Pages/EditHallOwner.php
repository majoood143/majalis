<?php

namespace App\Filament\Admin\Resources\HallOwnerResource\Pages;

use App\Filament\Admin\Resources\HallOwnerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Booking;
use App\Models\Hall;

class EditHallOwner extends EditRecord
{
    protected static string $resource = HallOwnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->icon('heroicon-o-eye')
                ->color('info'),

            Actions\Action::make('verify')
                ->label(fn() => $this->record->is_verified ? 'Unverify' : 'Verify Owner')
                ->icon(fn() => $this->record->is_verified ? 'heroicon-o-x-circle' : 'heroicon-o-check-badge')
                ->color(fn() => $this->record->is_verified ? 'warning' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_verified ? 'Unverify Hall Owner' : 'Verify Hall Owner')
                ->modalDescription(fn() => $this->record->is_verified
                    ? 'This will remove verification status from this owner.'
                    : 'This will verify the hall owner and enable their account.')
                ->form(fn() => !$this->record->is_verified ? [
                    \Filament\Forms\Components\Textarea::make('verification_notes')
                        ->label('Verification Notes')
                        ->rows(3),
                ] : [])
                ->action(function (array $data) {
                    if ($this->record->is_verified) {
                        $this->record->unverify();
                    } else {
                        $this->record->verify(Auth::id(), $data['verification_notes'] ?? null);
                    }

                    Notification::make()
                        ->success()
                        ->title('Verification Status Updated')
                        ->send();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('toggleActive')
                ->label(fn() => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_active ? 'Deactivate Owner' : 'Activate Owner')
                ->modalDescription(fn() => $this->record->is_active
                    ? 'This will deactivate the owner and prevent them from managing halls.'
                    : 'This will activate the owner account.')
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title('Status Updated')
                        ->send();

                    //Cache::tags(['hall_owners'])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('downloadDocuments')
                ->label('Download Documents')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $this->downloadAllDocuments();
                })
                ->visible(fn() => $this->hasDocuments()),

            Actions\Action::make('generateReport')
                ->label('Generate Owner Report')
                ->icon('heroicon-o-document-chart-bar')
                ->color('info')
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
                    $this->generateOwnerReport($data);
                }),

            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    if ($this->record->halls()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Delete Owner')
                            ->body('This owner has ' . $this->record->halls()->count() . ' hall(s). Please reassign or delete them first.')
                            ->persistent()
                            ->send();

                        $action->cancel();
                    }
                })
                ->after(function () {
                    $this->deleteDocuments();
                    //Cache::tags(['hall_owners'])->flush();
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Owner Deleted')
                        ->body('The hall owner has been deleted successfully.')
                ),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validate commercial registration uniqueness
        if (
            isset($data['commercial_registration']) &&
            $data['commercial_registration'] !== $this->record->commercial_registration
        ) {
            $exists = \App\Models\HallOwner::where('commercial_registration', $data['commercial_registration'])
                ->where('id', '!=', $this->record->id)
                ->exists();

            if ($exists) {
                Notification::make()
                    ->danger()
                    ->title('Duplicate Registration')
                    ->body('This commercial registration number already exists.')
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        // Clean phone number
        if (isset($data['business_phone'])) {
            $data['business_phone'] = preg_replace('/[^0-9+]/', '', $data['business_phone']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        //Cache::tags(['hall_owners'])->flush();

        Log::info('Hall owner updated', [
            'owner_id' => $this->record->id,
            'business_name' => $this->record->business_name,
            'updated_by' => Auth::id(),
        ]);
    }

    protected function hasDocuments(): bool
    {
        return $this->record->commercial_registration_document ||
            $this->record->tax_certificate ||
            $this->record->identity_document;
    }

    protected function downloadAllDocuments(): void
    {
        Notification::make()
            ->success()
            ->title('Documents Ready')
            ->body('All documents are being prepared for download.')
            ->send();
    }

    protected function deleteDocuments(): void
    {
        if ($this->record->commercial_registration_document) {
            Storage::disk('public')->delete($this->record->commercial_registration_document);
        }

        if ($this->record->tax_certificate) {
            Storage::disk('public')->delete($this->record->tax_certificate);
        }

        if ($this->record->identity_document) {
            Storage::disk('public')->delete($this->record->identity_document);
        }
    }

    protected function generateOwnerReport(array $data): void
    {
        try {
            $owner = $this->record;
            $fromDate = $data['from_date'];
            $toDate = $data['to_date'];

            // Get all halls owned by this owner
            $halls = Hall::where('owner_id', $owner->user_id)->get();

            // Get all bookings for these halls within the date range
            $bookings = Booking::whereIn('hall_id', $halls->pluck('id'))
                ->whereBetween('booking_date', [$fromDate, $toDate])
                ->with(['hall', 'extraServices', 'user'])
                ->orderBy('booking_date', 'desc')
                ->get();

            // Calculate statistics
            $stats = [
                'total_bookings' => $bookings->count(),
                'confirmed_bookings' => $bookings->filter(function ($b) {
                    return $b->status->value === 'confirmed';
                })->count(),
                'completed_bookings' => $bookings->filter(function ($b) {
                    return $b->status->value === 'completed';
                })->count(),
                'cancelled_bookings' => $bookings->filter(function ($b) {
                    return $b->status->value === 'cancelled';
                })->count(),
                'pending_bookings' => $bookings->filter(function ($b) {
                    return $b->status->value === 'pending';
                })->count(),

                'total_revenue' => $bookings->filter(function ($b) {
                    return in_array($b->status->value, ['confirmed', 'completed']) && $b->payment_status->value === 'paid';
                })->sum('total_amount'),

                'total_commission' => $bookings->filter(function ($b) {
                    return in_array($b->status->value, ['confirmed', 'completed']) && $b->payment_status->value === 'paid';
                })->sum('commission_amount'),

                'owner_payout' => $bookings->filter(function ($b) {
                    return in_array($b->status->value, ['confirmed', 'completed']) && $b->payment_status->value === 'paid';
                })->sum('owner_payout'),

                'total_guests' => $bookings->filter(function ($b) {
                    return in_array($b->status->value, ['confirmed', 'completed']);
                })->sum('number_of_guests'),

                'average_booking_value' => $bookings->filter(function ($b) {
                    return in_array($b->status->value, ['confirmed', 'completed']) && $b->payment_status->value === 'paid';
                })->avg('total_amount') ?? 0,
            ];

            // Hall performance
            $hallPerformance = $halls->map(function ($hall) use ($bookings) {
                $hallBookings = $bookings->where('hall_id', $hall->id);
                $paidBookings = $hallBookings->filter(function ($b) {
                    return in_array($b->status->value, ['confirmed', 'completed']) && $b->payment_status->value === 'paid';
                });

                $hallName = $hall->name;
                if (is_array($hallName)) {
                    $hallName = $hallName['en'] ?? $hallName['ar'] ?? 'Unknown Hall';
                }

                return [
                    'hall_name' => $hallName,
                    'bookings_count' => $hallBookings->count(),
                    'revenue' => $paidBookings->sum('total_amount'),
                    'payout' => $paidBookings->sum('owner_payout'),
                ];
            });

            // Monthly breakdown
            $monthlyBreakdown = $bookings->groupBy(function ($booking) {
                return $booking->booking_date->format('Y-m');
            })->map(function ($monthBookings) {
                $paid = $monthBookings->filter(function ($b) {
                    return in_array($b->status->value, ['confirmed', 'completed']) && $b->payment_status->value === 'paid';
                });

                return [
                    'month' => $monthBookings->first()->booking_date->format('F Y'),
                    'bookings' => $monthBookings->count(),
                    'revenue' => $paid->sum('total_amount'),
                    'commission' => $paid->sum('commission_amount'),
                    'payout' => $paid->sum('owner_payout'),
                ];
            })->values();

            // Generate PDF
            $pdf = Pdf::loadView('pdf.owner-report', [
                'owner' => $owner,
                'user' => $owner->user,
                'halls' => $halls,
                'bookings' => $bookings,
                'stats' => $stats,
                'hallPerformance' => $hallPerformance,
                'monthlyBreakdown' => $monthlyBreakdown,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'generatedAt' => now(),
                'generatedBy' => Auth::user()->name,
            ])->setPaper('a4');

            // Ensure directory exists
            if (!Storage::disk('public')->exists('reports')) {
                Storage::disk('public')->makeDirectory('reports');
            }

            $filename = 'owner-report-' . $owner->id . '-' . now()->format('Y-m-d-His') . '.pdf';
            $filepath = 'reports/' . $filename;

            Storage::disk('public')->put($filepath, $pdf->output());

            // Log the report generation
            Log::info('Owner report generated', [
                'owner_id' => $owner->id,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'generated_by' => Auth::id(),
                'filename' => $filename,
            ]);

            // Send success notification with download link
            Notification::make()
                ->success()
                ->title('Report Generated Successfully')
                ->body('Hall owner performance report has been created.')
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label('Download Report')
                        ->url(asset('storage/' . $filepath))
                        ->openUrlInNewTab(),
                ])
                ->send();
        } catch (\Exception $e) {
            Log::error('Failed to generate owner report', [
                'owner_id' => $this->record->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->danger()
                ->title('Report Generation Failed')
                ->body('An error occurred while generating the report. Please try again.')
                ->persistent()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                //->submit(null)
                ->keyBindings(['mod+s']),

            $this->getCancelFormAction(),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit Owner: ' . $this->record->business_name;
    }

    public function getSubheading(): ?string
    {
        return 'Update hall owner information and settings';
    }
}
