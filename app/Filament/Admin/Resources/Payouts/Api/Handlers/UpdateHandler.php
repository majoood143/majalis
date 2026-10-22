<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payouts\Api\Handlers;

use App\Filament\Admin\Resources\PayoutResource;
use App\Filament\Admin\Resources\Payouts\Api\Requests\UpdatePayoutRequest;
use App\Filament\Admin\Resources\Payouts\Api\Transformers\PayoutTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Payout record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = PayoutResource::class;

    protected static string $permission = 'Update:OwnerPayout';

    protected static string $transformer = PayoutTransformer::class;

    /**
     * Update Payout
     */
    public function handler(UpdatePayoutRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        // net = gross - commission + adjustments (same as the panel)
        $gross = (float) ($data['gross_revenue'] ?? $record->gross_revenue);
        $commission = (float) ($data['commission_amount'] ?? $record->commission_amount);
        $adjustments = (float) ($data['adjustments'] ?? $record->adjustments);

        $data['net_payout'] = max(0, $gross - $commission + $adjustments);

        if ($gross > 0) {
            $data['commission_rate'] = ($commission / $gross) * 100;
        }

        return $data;
    }
}
