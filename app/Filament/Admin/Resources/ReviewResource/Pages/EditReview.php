<?php

namespace App\Filament\Admin\Resources\ReviewResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditReview extends EditRecord
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label(fn() => $this->record->is_approved ? 'Disapprove' : 'Approve')
                ->icon('heroicon-o-check-circle')
                ->color(fn() => $this->record->is_approved ? 'warning' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    if ($this->record->is_approved) {
                        $this->record->update(['is_approved' => false]);
                    } else {
                        $this->record->approve();
                    }

                    Notification::make()->success()->title('Status Updated')->send();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Action::make('toggleFeatured')
                ->label(fn() => $this->record->is_featured ? 'Unmark Featured' : 'Mark Featured')
                ->icon('heroicon-o-star')
                ->color('warning')
                ->action(function () {
                    $this->record->is_featured = !$this->record->is_featured;
                    $this->record->save();

                    Notification::make()->success()->title('Featured Status Updated')->send();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        Cache::tags(['reviews', 'hall_' . $this->record->hall_id])->flush();
    }
}
