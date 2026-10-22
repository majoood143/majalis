<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Expenses\Api\Handlers;

use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;
use App\Filament\Owner\Resources\ExpenseResource;

/**
 * Delete a Expense record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = ExpenseResource::class;

    protected static string $permission = 'Delete:Expense';

    protected static bool $checkPermission = false;
}
