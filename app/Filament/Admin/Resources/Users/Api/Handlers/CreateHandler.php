<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Api\Handlers;

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Admin\Resources\Users\Api\Requests\CreateUserRequest;
use App\Filament\Admin\Resources\Users\Api\Transformers\UserTransformer;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;

/**
 * Create a User record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = UserResource::class;

    protected static string $permission = 'Create:User';

    protected static string $transformer = UserTransformer::class;

    /**
     * Create User
     */
    public function handler(CreateUserRequest $request)
    {
        return $this->store($request);
    }
}
