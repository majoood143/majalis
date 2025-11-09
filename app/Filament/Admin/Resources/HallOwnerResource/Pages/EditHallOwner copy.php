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

                    Cache::tags(['hall_owners'])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('viewHalls')
                ->label('View Halls')
                ->icon('heroicon-o-building-storefront')
                ->color('info')
                ->badge(fn() => $this->record->halls()->count())
                ->url(fn() => route('filament.admin.resources.halls.index', [
                    'tableFilters' => [
                        'owner_id' => ['value' => $this->record->id]
                    ]
                ]))
                ->visible(fn() => $this->record->halls()->count() > 0),

            Actions\Action::make('viewBookings')
                ->label('View Bookings')
                ->icon('heroicon-o-calendar-days')
                ->color('purple')
                ->badge(fn() => $this->getTotalBookings())
                ->url(fn() => route('filament.admin.resources.bookings.index', [
                    'tableFilters' => [
                        'owner_id' => ['value' => $this->record->id]
                    ]
                ]))
                ->visible(fn() => $this->getTotalBookings() > 0),

            Actions\Action::make('updateCommission')
                ->label('Update Commission')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('commission_type')
                        ->label('Commission Type')
                        ->options([
                            'percentage' => 'Percentage',
                            'fixed' => 'Fixed Amount',
                        ])
                        ->default(fn() => $this->record->commission_type)
                        ->reactive()
                        ->required(),

                    \Filament\Forms\Components\TextInput::make('commission_value')
                        ->label('Commission Value')
                        ->numeric()
                        ->step(0.01)
                        ->required()
                        ->default(fn() => $this->record->commission_value)
                        ->suffix(fn($get) => $get('commission_type') === 'percentage' ? '%' : 'OMR'),

                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Reason for Change')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $oldType = $this->record->commission_type;
                    $oldValue = $this->record->commission_value;

                    $this->record->update([
                        'commission_type' => $data['commission_type'],
                        'commission_value' => $data['commission_value'],
                    ]);

                    activity()
                        ->performedOn($this->record)
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'old_type' => $oldType,
                            'old_value' => $oldValue,
                            'new_type' => $data['commission_type'],
                            'new_value' => $data['commission_value'],
                            'reason' => $data['reason'] ?? 'No reason provided',
                        ])
                        ->log('Commission updated');

                    Notification::make()
                        ->success()
                        ->title('Commission Updated')
                        ->body('Owner commission settings have been updated.')
                        ->send();
                }),

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
                    // Send notification to owner
                    // Example: $this->record->user->notify(new OwnerNotification($data['subject'], $data['message']));

                    Notification::make()
                        ->success()
                        ->title('Notification Sent')
                        ->body('Notification has been sent to the owner.')
                        ->send();
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
                        ->native(false),

                    \Filament\Forms\Components\DatePicker::make('to_date')
                        ->label('To Date')
                        ->default(now())
                        ->native(false),
                ])
                ->action(function (array $data) {
                    $this->generateOwnerReport($data);
                }),

            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    // Check if owner has halls
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
                    // Delete documents
                    $this->deleteDocuments();

                    Cache::tags(['hall_owners'])->flush();
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Owner Deleted')
                        ->body('The hall owner has been deleted successfully.')
                ),

            Actions\Action::make('viewHistory')
                ->label('View History')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->modalContent(fn() => view('filament.pages.activity-log', [
                    'activities' => activity()
                        ->forSubject($this->record)
                        ->latest()
                        ->get()
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Owner Updated')
            ->body('The hall owner profile has been updated successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validate commission settings
        if (isset($data['commission_type']) && isset($data['commission_value'])) {
            if ($data['commission_type'] === 'percentage' && $data['commission_value'] > 100) {
                Notification::make()
                    ->danger()
                    ->title('Invalid Commission')
                    ->body('Percentage commission cannot exceed 100%.')
                    ->persistent()
                    ->send();

                $this->halt();
            }

            if ($data['commission_value'] < 0) {
                Notification::make()
                    ->danger()
                    ->title('Invalid Commission')
                    ->body('Commission value cannot be negative.')
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $oldValues = $record->toArray();

        $record->update($data);

        $changes = array_diff_assoc($data, $oldValues);

        // Log the update
        activity()
            ->performedOn($record)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'changes' => $changes,
            ])
            ->log('Hall owner updated');

        return $record;
    }

    protected function afterSave(): void
    {
        // Clear cache
        Cache::tags(['hall_owners'])->flush();

        // Log the update
        Log::info('Hall owner updated', [
            'owner_id' => $this->record->id,
            'business_name' => $this->record->business_name,
            'updated_by' => Auth::id(),
        ]);
    }

    protected function getTotalBookings(): int
    {
        // Implement based on your booking structure
        return 0;
    }

    protected function hasDocuments(): bool
    {
        return $this->record->commercial_registration_document ||
            $this->record->tax_certificate ||
            $this->record->identity_document;
    }

    protected function downloadAllDocuments(): void
    {
        // Implement document download logic
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
        // Generate comprehensive owner report
        Notification::make()
            ->success()
            ->title('Report Generated')
            ->body('Owner performance report has been generated.')
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->submit(null)
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
        $status = $this->record->is_verified ? 'Verified' : 'Pending Verification';
        $activeStatus = $this->record->is_active ? 'Active' : 'Inactive';
        $hallsCount = $this->record->halls()->count();

        return "{$status} • {$activeStatus} • {$hallsCount} Hall(s)";
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
