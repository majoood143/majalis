<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\Pages;

use App\Filament\Owner\Resources\HallResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

/**
 * ViewHall Page for Owner Panel
 *
 * Displays detailed hall information for the owner.
 */
class ViewHall extends ViewRecord
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = HallResource::class;

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return $this->record->getTranslation('name', app()->getLocale());
    }

    /**
     * Get the header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // Edit Hall
            Actions\EditAction::make()
                ->label(__('owner.halls.actions.edit'))
                ->icon('heroicon-o-pencil'),

            // Manage Availability
            Actions\Action::make('availability')
                ->label(__('owner.halls.actions.availability'))
                ->icon('heroicon-o-calendar')
                ->color('info')
                ->url(fn () => HallResource::getUrl('availability', ['record' => $this->record])),

            // View on Website
            Actions\Action::make('view_public')
                ->label(__('owner.halls.actions.view_public'))
                ->icon('heroicon-o-globe-alt')
                ->color('gray')
                //->url(fn () => route('halls.index', $this->record->slug))
                ->openUrlInNewTab(),
        ];
    }

    /**
     * Get the relation managers to display.
     *
     * @return array<class-string>
     */
    public function getRelationManagers(): array
    {
        return [
            // Show relation managers in view mode too
        ];
    }
}
