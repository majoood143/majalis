<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallOwners\Api\Handlers;

use App\Filament\Admin\Resources\HallOwnerResource;
use App\Filament\Admin\Resources\HallOwners\Api\Requests\UpdateHallOwnerRequest;
use App\Filament\Admin\Resources\HallOwners\Api\Transformers\HallOwnerTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Hall Owner record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = HallOwnerResource::class;

    protected static string $permission = 'Update:HallOwner';

    protected static string $transformer = HallOwnerTransformer::class;

    protected static array $fileFields = ['logo' => 'logos/hall-owners', 'commercial_registration_document' => 'documents/cr', 'tax_certificate' => 'documents/tax', 'identity_document' => 'documents/identity'];

    /**
     * Update Hall Owner
     */
    public function handler(UpdateHallOwnerRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        return CreateHandler::validateOwner($data, $record);
    }
}
