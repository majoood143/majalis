<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallCategories\Api\Handlers;

use App\Filament\Admin\Resources\HallCategoryResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Hall Category record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = HallCategoryResource::class;

    protected static string $permission = 'Delete:HallCategory';
}
