<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Expenses\Api\Handlers;

use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use App\Filament\Owner\Resources\ExpenseResource;
use App\Filament\Owner\Resources\Expenses\Api\Requests\UpdateExpenseRequest;
use App\Filament\Owner\Resources\Expenses\Api\Transformers\ExpenseTransformer;

/**
 * Update a Expense record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = ExpenseResource::class;

    protected static string $permission = 'Update:Expense';

    protected static bool $checkPermission = false;

    protected static string $transformer = ExpenseTransformer::class;

    protected static array $fileFields = ['attachments' => 'expenses/attachments'];

    /**
     * Update Expense
     */
    public function handler(UpdateExpenseRequest $request)
    {
        return $this->modify($request);
    }
}
