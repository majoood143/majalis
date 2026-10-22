<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Expenses\Api\Handlers;

use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use App\Filament\Owner\Resources\ExpenseResource;
use App\Filament\Owner\Resources\Expenses\Api\Requests\CreateExpenseRequest;
use App\Filament\Owner\Resources\Expenses\Api\Transformers\ExpenseTransformer;

/**
 * Create a Expense record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = ExpenseResource::class;

    protected static string $permission = 'Create:Expense';

    protected static bool $checkPermission = false;

    protected static string $transformer = ExpenseTransformer::class;

    protected static array $fileFields = ['attachments' => 'expenses/attachments'];

    /**
     * Create Expense
     */
    public function handler(CreateExpenseRequest $request)
    {
        return $this->store($request);
    }

    protected function beforeCreate(array $data): array
    {
        // Same owner value the panel uses (and that ExpenseResource::getEloquentQuery() filters on)
        $data['owner_id'] = auth()->user()?->hallOwner?->id ?? auth()->id();
        $data['created_by'] = auth()->id();

        return $data;
    }
}
