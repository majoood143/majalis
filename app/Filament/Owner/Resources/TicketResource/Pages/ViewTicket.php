<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\TicketResource\Pages;

use App\Filament\Owner\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
