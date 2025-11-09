<?php

namespace App\Filament\Admin\Resources\HallOwnerResource\Pages;

use App\Filament\Admin\Resources\HallOwnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Booking;
use App\Models\Hall;


class ViewHallOwner extends ViewRecord
{
    protected static string $resource = HallOwnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),

            Actions\Action::make('verify')
                ->label(fn() => $this->record->is_verified ? 'Unverify' : 'Verify')
                ->icon(fn() => $this->record->is_verified ? 'heroicon-o-x-circle' : 'heroicon-o-check-badge')
                ->color(fn() => $this->record->is_verified ? 'warning' : 'success')
                ->requiresConfirmation()
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
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title('Status Updated')
                        ->send();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('viewHalls')
                ->label('View Halls')
                ->icon('heroicon-o-building-storefront')
                ->color('info')
                ->badge(fn() => $this->record->halls()->count())
                ->url(fn() => route('filament.admin.resources.halls.index', [
                    'tableFilters' => [
                        //'owner_id' => ['value' => $this->record->id]
                    'owner_id' => ['value' => $this->record->user_id]
                    ]
                ])),

            Actions\Action::make('sendNotification')
                ->label('Send Notification')
                ->icon('heroicon-o-bell')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\TextInput::make('subject')
                        ->required()
                        ->maxLength(255),

                    \Filament\Forms\Components\Textarea::make('message')
                        ->required()
                        ->rows(5),
                ])
                ->action(function (array $data) {
                    Notification::make()
                        ->success()
                        ->title('Notification Sent')
                        ->body('Notification has been sent to the owner.')
                        ->send();
                }),

            // Actions\Action::make('generateReport')
            //     ->label('Generate Report')
            //     ->icon('heroicon-o-document-chart-bar')
            //     ->color('warning')
            //     ->action(function () {
            //         Notification::make()
            //             ->success()
            //             ->title('Report Generated')
            //             ->send();
            //     }),
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
                            ->title('Cannot Delete')
                            ->body('This owner has halls.')
                            ->persistent()
                            ->send();

                        $action->cancel();
                    }
                })
                ->successRedirectUrl(route('filament.admin.resources.hall-owners.index')),
        ];
    }

    public function getTitle(): string
    {
        return 'Hall Owner: ' . $this->record->business_name;
    }

    public function getSubheading(): ?string
    {
        $status = $this->record->is_verified ? 'Verified' : 'Pending Verification';
        $activeStatus = $this->record->is_active ? 'Active' : 'Inactive';
        $hallsCount = $this->record->halls()->count();

        return "{$status} • {$activeStatus} • {$hallsCount} Hall(s) • CR: {$this->record->commercial_registration}";
    }

    public function getBreadcrumb(): string
    {
        return $this->record->business_name;
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
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
}
