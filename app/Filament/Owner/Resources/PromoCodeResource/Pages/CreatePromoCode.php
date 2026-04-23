<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PromoCodeResource\Pages;

use App\Filament\Owner\Resources\PromoCodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePromoCode extends CreateRecord
{
    protected static string $resource = PromoCodeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_type'] = 'hall_owner';
        $data['created_by_id']   = auth()->id();

        return $data;
    }
}
