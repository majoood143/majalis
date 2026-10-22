<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Expenses\Api\Handlers;

use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;
use App\Filament\Owner\Resources\ExpenseResource;
use App\Filament\Owner\Resources\Expenses\Api\Transformers\ExpenseTransformer;

/**
 * Show one Expense record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = ExpenseResource::class;

    protected static string $permission = 'View:Expense';

    protected static bool $checkPermission = false;

    protected static string $transformer = ExpenseTransformer::class;

    protected static array $allowedIncludes = ['hall', 'booking', 'category'];
}
