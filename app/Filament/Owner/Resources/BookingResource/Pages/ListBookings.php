<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\BookingResource\Pages;

use App\Filament\Owner\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Owners typically don't create bookings directly
            // Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return __('owner.bookings.title');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // We'll add widgets in Part 4
        ];
    }
}
