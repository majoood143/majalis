<?php

namespace App\Filament\Admin\Resources\ReviewResource\Pages;

use App\Filament\Admin\Resources\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Notifications\Notification;

class ViewReview extends ViewRecord
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('approve')
                ->visible(fn() => !$this->record->is_approved)
                ->action(function () {
                    $this->record->approve();
                    Notification::make()->success()->title('Review Approved')->send();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Review Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('hall.name')
                            ->formatStateUsing(fn($record) => $record->hall->getTranslation('name', 'en')),
                        Infolists\Components\TextEntry::make('user.name'),
                        Infolists\Components\TextEntry::make('rating')
                            ->badge()
                            ->formatStateUsing(fn($state) => str_repeat('⭐', $state)),
                        Infolists\Components\TextEntry::make('comment')->columnSpanFull(),
                    ])->columns(3),

                Infolists\Components\Section::make('Detailed Ratings')
                    ->schema([
                        Infolists\Components\TextEntry::make('cleanliness_rating')->suffix('/5'),
                        Infolists\Components\TextEntry::make('service_rating')->suffix('/5'),
                        Infolists\Components\TextEntry::make('value_rating')->suffix('/5'),
                        Infolists\Components\TextEntry::make('location_rating')->suffix('/5'),
                    ])->columns(4),

                Infolists\Components\Section::make('Status')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_approved')->boolean(),
                        Infolists\Components\IconEntry::make('is_featured')->boolean(),
                    ])->columns(2),
            ]);
    }
}
