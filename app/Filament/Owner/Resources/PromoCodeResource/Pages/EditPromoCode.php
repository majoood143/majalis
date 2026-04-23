<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PromoCodeResource\Pages;

use App\Filament\Owner\Resources\PromoCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPromoCode extends EditRecord
{
    protected static string $resource = PromoCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
