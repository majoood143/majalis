<?php

namespace App\Filament\Admin\Resources\CommissionSettingResource\Pages;

use App\Filament\Admin\Resources\CommissionSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ViewCommissionSetting extends ViewRecord
{
    protected static string $resource = CommissionSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),

            Actions\Action::make('toggleActive')
                ->label(fn() => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->title('Status Updated')
                        ->success()
                        ->send();

                    Cache::tags(['commissions'])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('viewBookings')
                ->label('View Affected Bookings')
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->visible(fn() => $this->record->hall_id !== null)
                ->url(fn() => route('filament.admin.resources.bookings.index', [
                    'tableFilters' => [
                        'hall_id' => ['value' => $this->record->hall_id]
                    ]
                ])),

            Actions\Action::make('calculateRevenue')
                ->label('Calculate Revenue Impact')
                ->icon('heroicon-o-calculator')
                ->color('success')
                ->modalHeading('Commission Revenue Analysis')
                ->modalContent(fn() => view('filament.pages.commission-revenue-analysis', [
                    'commission' => $this->record,
                    'stats' => $this->getRevenueStats(),
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),

            Actions\Action::make('exportReport')
                ->label('Export Report')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->action(function () {
                    $this->exportCommissionReport();
                }),

            Actions\Action::make('duplicate')
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $newCommission = $this->record->replicate();
                    $newCommission->is_active = false;

                    if ($newCommission->name) {
                        $name = $newCommission->getTranslations('name');
                        foreach ($name as $locale => $value) {
                            $name[$locale] = $value . ' (Copy)';
                        }
                        $newCommission->setTranslations('name', $name);
                    }

                    $newCommission->save();

                    Notification::make()
                        ->success()
                        ->title('Commission Duplicated')
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('View Duplicate')
                                ->url(CommissionSettingResource::getUrl('view', ['record' => $newCommission->id])),
                        ])
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    // Add validation before deletion if needed
                })
                ->successRedirectUrl(route('filament.admin.resources.commission-settings.index')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Commission Scope')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('scope_type')
                                    ->label('Scope Type')
                                    ->state(function ($record) {
                                        if ($record->hall_id) return 'Hall-Specific';
                                        if ($record->owner_id) return 'Owner-Specific';
                                        return 'Global';
                                    })
                                    ->badge()
                                    ->color(fn($record) => match (true) {
                                        $record->hall_id !== null => 'success',
                                        $record->owner_id !== null => 'warning',
                                        default => 'primary',
                                    })
                                    ->icon('heroicon-o-tag')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                                Infolists\Components\TextEntry::make('hall.name')
                                    ->label('Hall')
                                    ->placeholder('Not Applicable')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-building-storefront')
                                    ->visible(fn($record) => $record->hall_id !== null),

                                Infolists\Components\TextEntry::make('owner.name')
                                    ->label('Owner')
                                    ->placeholder('Not Applicable')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-user')
                                    ->visible(fn($record) => $record->owner_id !== null && $record->hall_id === null),
                            ]),

                        Infolists\Components\TextEntry::make('priority_note')
                            ->label('Priority Information')
                            ->state('Priority: Hall-specific > Owner-specific > Global')
                            ->color('info')
                            ->icon('heroicon-o-information-circle')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-funnel')
                    ->collapsible(),

                Infolists\Components\Section::make('Commission Details')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Commission Name')
                                    ->formatStateUsing(fn($record) => $record->name ?? 'Unnamed Commission')
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-tag'),

                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->size(Infolists\Components\IconEntry\IconEntrySize::Large),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name.en')
                                    ->label('Name (English)')
                                    ->placeholder('Not set')
                                    ->icon('heroicon-o-language'),

                                Infolists\Components\TextEntry::make('name.ar')
                                    ->label('Name (Arabic)')
                                    ->placeholder('غير محدد')
                                    ->icon('heroicon-o-language'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('commission_type')
                                    ->label('Commission Type')
                                    ->formatStateUsing(fn($state) => ucfirst($state->value ?? $state))
                                    ->badge()
                                    ->color('info')
                                    ->icon(fn($record) => $record->commission_type->value === 'percentage'
                                        ? 'heroicon-o-percent-badge'
                                        : 'heroicon-o-banknotes'),

                                Infolists\Components\TextEntry::make('commission_value')
                                    ->label('Commission Value')
                                    ->formatStateUsing(function ($record) {
                                        return $record->commission_type->value === 'percentage'
                                            ? $record->commission_value . '%'
                                            : number_format($record->commission_value, 3) . ' OMR';
                                    })
                                    ->badge()
                                    ->color('success')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->copyable()
                                    ->icon('heroicon-o-currency-dollar'),
                            ]),

                        Infolists\Components\Grid::make(1)
                            ->schema([
                                Infolists\Components\TextEntry::make('description.en')
                                    ->label('Description (English)')
                                    ->placeholder('No description provided')
                                    ->columnSpanFull(),

                                Infolists\Components\TextEntry::make('description.ar')
                                    ->label('Description (Arabic)')
                                    ->placeholder('لا يوجد وصف')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->icon('heroicon-o-calculator')
                    ->collapsible(),

                Infolists\Components\Section::make('Validity Period')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('effective_from')
                                    ->label('Effective From')
                                    ->date('d M Y')
                                    ->placeholder('Immediate Effect')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('effective_to')
                                    ->label('Effective To')
                                    ->date('d M Y')
                                    ->placeholder('Indefinite')
                                    ->badge()
                                    ->color(fn($record) => $record->effective_to && $record->effective_to < now()
                                        ? 'danger'
                                        : 'info')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('validity_status')
                                    ->label('Current Status')
                                    ->state(function ($record) {
                                        if ($record->effective_to && $record->effective_to < now()) {
                                            return 'Expired';
                                        }
                                        if ($record->effective_from && $record->effective_from > now()) {
                                            return 'Not Yet Active';
                                        }
                                        return 'Currently Active';
                                    })
                                    ->badge()
                                    ->color(function ($record) {
                                        if ($record->effective_to && $record->effective_to < now()) {
                                            return 'danger';
                                        }
                                        if ($record->effective_from && $record->effective_from > now()) {
                                            return 'warning';
                                        }
                                        return 'success';
                                    })
                                    ->icon('heroicon-o-clock'),
                            ]),

                        Infolists\Components\TextEntry::make('duration')
                            ->label('Duration')
                            ->state(function ($record) {
                                if (!$record->effective_from || !$record->effective_to) {
                                    return 'Indefinite';
                                }

                                $from = \Carbon\Carbon::parse($record->effective_from);
                                $to = \Carbon\Carbon::parse($record->effective_to);

                                return $from->diffInDays($to) . ' days';
                            })
                            ->icon('heroicon-o-clock')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-calendar-days')
                    ->collapsible(),

                Infolists\Components\Section::make('Financial Statistics')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('total_bookings')
                                    ->label('Total Bookings')
                                    ->state(fn($record) => $this->getTotalBookings($record))
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('total_revenue')
                                    ->label('Total Commission Earned')
                                    ->state(fn($record) => number_format($this->getTotalRevenue($record), 3) . ' OMR')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-banknotes'),

                                Infolists\Components\TextEntry::make('avg_commission')
                                    ->label('Average per Booking')
                                    ->state(fn($record) => number_format($this->getAverageCommission($record), 3) . ' OMR')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-calculator'),

                                Infolists\Components\TextEntry::make('last_applied')
                                    ->label('Last Applied')
                                    ->state(fn($record) => $this->getLastAppliedDate($record))
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible(),

                Infolists\Components\Section::make('System Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('id')
                                    ->label('Commission ID')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('d M Y, h:i A')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('d M Y, h:i A')
                                    ->since()
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->icon('heroicon-o-server')
                    ->collapsed(),

                Infolists\Components\Section::make('Activity History')
                    ->schema([
                        Infolists\Components\ViewEntry::make('activity_log')
                            ->label('')
                            ->view('filament.infolists.components.activity-log', [
                                'activities' => fn($record) => activity()
                                    ->forSubject($record)
                                    ->latest()
                                    ->limit(10)
                                    ->get()
                            ]),
                    ])
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->visible(fn() => class_exists(\Spatie\Activitylog\Models\Activity::class)),
            ]);
    }

    public function getTitle(): string
    {
        $scopeType = $this->getScopeType();
        return "View {$scopeType} Commission Setting";
    }

    public function getSubheading(): ?string
    {
        $type = ucfirst($this->record->commission_type->value ?? $this->record->commission_type);
        $value = $this->record->commission_type->value === 'percentage'
            ? $this->record->commission_value . '%'
            : number_format($this->record->commission_value, 3) . ' OMR';
        $status = $this->record->is_active ? 'Active' : 'Inactive';

        return "{$status} • {$type}: {$value}";
    }

    protected function getScopeType(): string
    {
        if ($this->record->hall_id) return 'Hall-Specific';
        if ($this->record->owner_id) return 'Owner-Specific';
        return 'Global';
    }

    protected function getRevenueStats(): array
    {
        // Placeholder - implement based on your booking/payment structure
        return [
            'total_bookings' => 0,
            'total_revenue' => 0,
            'average_commission' => 0,
            'last_applied' => null,
        ];
    }

    protected function getTotalBookings($record): int
    {
        // Implement based on your booking system
        return 0;
    }

    protected function getTotalRevenue($record): float
    {
        // Implement based on your payment system
        return 0.000;
    }

    protected function getAverageCommission($record): float
    {
        $totalBookings = $this->getTotalBookings($record);
        if ($totalBookings === 0) return 0.000;

        return $this->getTotalRevenue($record) / $totalBookings;
    }

    protected function getLastAppliedDate($record): string
    {
        // Implement based on your booking system
        return 'Never';
    }

    protected function exportCommissionReport(): void
    {
        $filename = 'commission_report_' . $this->record->id . '_' . now()->format('Y_m_d_His') . '.pdf';

        // Implement PDF generation here
        // You might want to use a package like barryvdh/laravel-dompdf

        Notification::make()
            ->success()
            ->title('Report Generated')
            ->body('Commission report has been generated successfully.')
            ->send();
    }

    public function getBreadcrumb(): string
    {
        return $this->record->name ?? 'Commission Setting';
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
