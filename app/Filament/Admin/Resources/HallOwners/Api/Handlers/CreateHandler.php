<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallOwners\Api\Handlers;

use App\Filament\Admin\Resources\HallOwnerResource;
use App\Filament\Admin\Resources\HallOwners\Api\Requests\CreateHallOwnerRequest;
use App\Filament\Admin\Resources\HallOwners\Api\Transformers\HallOwnerTransformer;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Create a Hall Owner record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = HallOwnerResource::class;

    protected static string $permission = 'Create:HallOwner';

    protected static string $transformer = HallOwnerTransformer::class;

    protected static array $fileFields = ['logo' => 'logos/hall-owners', 'commercial_registration_document' => 'documents/cr', 'tax_certificate' => 'documents/tax', 'identity_document' => 'documents/identity'];

    /**
     * Create Hall Owner
     */
    public function handler(CreateHallOwnerRequest $request)
    {
        return $this->store($request);
    }

    protected function beforeCreate(array $data): array
    {
        return static::validateOwner($data);
    }

    public static function validateOwner(array $data, ?Model $record = null): array
    {
        $type = $data['commission_type'] ?? $record?->commission_type;
        $value = $data['commission_value'] ?? $record?->commission_value;

        if ($type === 'percentage' && $value > 100) {
            static::fail('commission_value', __('Percentage commission cannot exceed 100%.'));
        }

        if (! empty($data['business_phone'])) {
            $data['business_phone'] = preg_replace('/[^0-9+]/', '', $data['business_phone']);
        }

        return $data;
    }

    protected function afterCreate(Model $record, array $data): void
    {
        activity()
            ->performedOn($record)
            ->causedBy(auth()->user())
            ->withProperties(['source' => 'api', 'changes' => $data])
            ->log(class_basename($record) . ' Created via API');
    }
}
