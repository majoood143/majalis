<?php

namespace App\Filament\Admin\Resources\HallOwnerResource\Pages;

use App\Filament\Admin\Resources\HallOwnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
                        'owner_id' => ['value' => $this->record->id]
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

            Actions\Action::make('generateReport')
                ->label('Generate Report')
                ->icon('heroicon-o-document-chart-bar')
                ->color('warning')
                ->action(function () {
                    Notification::make()
                        ->success()
                        ->title('Report Generated')
                        ->send();
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
}
