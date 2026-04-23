<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PromoCodeResource\Pages;

use App\Filament\Admin\Resources\PromoCodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePromoCode extends CreateRecord
{
    protected static string $resource = PromoCodeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_type'] = 'admin';
        $data['created_by_id']   = auth()->id();

        return $data;
    }
}
