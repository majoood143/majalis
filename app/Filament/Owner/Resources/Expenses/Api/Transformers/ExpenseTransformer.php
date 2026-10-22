<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Expenses\Api\Transformers;

use App\Filament\Api\Transformers\Transformer;
use App\Models\Expense;

/**
 * @property Expense $resource
 */
class ExpenseTransformer extends Transformer
{
    protected array $files = ['attachments'];
}
