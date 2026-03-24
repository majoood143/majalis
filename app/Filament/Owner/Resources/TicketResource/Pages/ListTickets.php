<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\TicketResource\Pages;

use App\Filament\Owner\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
