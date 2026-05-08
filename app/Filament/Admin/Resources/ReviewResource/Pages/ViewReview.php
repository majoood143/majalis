<?php

namespace App\Filament\Admin\Resources\ReviewResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use App\Filament\Admin\Resources\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Notifications\Notification;

class ViewReview extends ViewRecord
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            Action::make('approve')
                ->visible(fn() => !$this->record->is_approved)
                ->action(function () {
                    $this->record->approve();
                    Notification::make()->success()->title('Review Approved')->send();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $infolist
            ->schema([
                Section::make('Review Information')
                    ->schema([
                        TextEntry::make('hall.name')
                            ->formatStateUsing(fn($record) => $record->hall->getTranslation('name', 'en')),
                        TextEntry::make('user.name'),
                        TextEntry::make('rating')
                            ->badge()
                            ->formatStateUsing(fn($state) => str_repeat('⭐', $state)),
                        TextEntry::make('comment')->columnSpanFull(),
                    ])->columns(3),

                Section::make('Detailed Ratings')
                    ->schema([
                        TextEntry::make('cleanliness_rating')->suffix('/5'),
                        TextEntry::make('service_rating')->suffix('/5'),
                        TextEntry::make('value_rating')->suffix('/5'),
                        TextEntry::make('location_rating')->suffix('/5'),
                    ])->columns(4),

                Section::make('Status')
                    ->schema([
                        IconEntry::make('is_approved')->boolean(),
                        IconEntry::make('is_featured')->boolean(),
                    ])->columns(2),
            ]);
    }
}
