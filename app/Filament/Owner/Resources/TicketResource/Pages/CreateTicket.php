<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\TicketResource\Pages;

use App\Filament\Owner\Resources\TicketResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }
}
