<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Expenses\Api\Handlers;

use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;
use App\Filament\Owner\Resources\ExpenseResource;
use App\Filament\Owner\Resources\Expenses\Api\Transformers\ExpenseTransformer;

/**
 * List Expense records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = ExpenseResource::class;

    protected static string $permission = 'ViewAny:Expense';

    protected static bool $checkPermission = false;

    protected static string $transformer = ExpenseTransformer::class;

    protected static array $allowedIncludes = ['hall', 'booking', 'category'];

    protected static array $allowedFilters = ['expense_type', 'hall_id', 'booking_id', 'category_id', 'payment_status', 'payment_method', 'status', 'is_recurring'];

    protected static array $allowedSorts = ['id', 'expense_date', 'amount', 'created_at'];

    protected static array $searchable = ['expense_number', 'title->en', 'title->ar', 'vendor_name'];

    protected static array $dateFilters = ['expense_date'];
}
