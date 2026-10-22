<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Api\Handlers;

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Admin\Resources\Users\Api\Requests\UpdateUserRequest;
use App\Filament\Admin\Resources\Users\Api\Transformers\UserTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a User record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = UserResource::class;

    protected static string $permission = 'Update:User';

    protected static string $transformer = UserTransformer::class;

    /**
     * Update User
     */
    public function handler(UpdateUserRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        // Empty password = keep the current one
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}
