<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payments\Api\Handlers;

use App\Filament\Admin\Resources\PaymentResource;
use App\Filament\Admin\Resources\Payments\Api\Requests\UpdatePaymentRequest;
use App\Filament\Admin\Resources\Payments\Api\Transformers\PaymentTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Payment record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = PaymentResource::class;

    protected static string $permission = 'Update:Payment';

    protected static string $transformer = PaymentTransformer::class;

    /**
     * Update Payment
     */
    public function handler(UpdatePaymentRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        $old = $record->status instanceof \BackedEnum ? $record->status->value : $record->status;
        $new = $data['status'] ?? $old;

        // Same blocked transitions as the panel
        $invalid = ['paid' => ['pending'], 'refunded' => ['pending', 'paid']];

        if (in_array($new, $invalid[$old] ?? [], true)) {
            static::fail('status', __('Cannot change status from :from to :to', ['from' => $old, 'to' => $new]));
        }

        if ($new !== $old) {
            $stamp = ['paid' => 'paid_at', 'failed' => 'failed_at', 'refunded' => 'refunded_at', 'partially_refunded' => 'refunded_at'][$new] ?? null;

            if ($stamp && ! $record->{$stamp}) {
                $data[$stamp] = now();
            }
        }

        return $data;
    }
}
