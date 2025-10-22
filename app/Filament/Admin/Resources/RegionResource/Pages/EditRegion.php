<?php

namespace App\Filament\Admin\Resources\RegionResource\Pages;

use App\Filament\Admin\Resources\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditRegion extends EditRecord
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewCities')
                ->label('View Cities')
                ->icon('heroicon-o-building-office')
                ->badge(fn() => $this->record->cities()->count())
                ->url(fn() => route('filament.admin.resources.cities.index', [
                    'tableFilters' => ['region_id' => ['value' => $this->record->id]]
                ])),

            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    if ($this->record->cities()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Delete')
                            ->body('This region has cities.')
                            ->persistent()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }

    protected function afterSave(): void
    {
        Cache::tags(['regions'])->flush();
    }
}
