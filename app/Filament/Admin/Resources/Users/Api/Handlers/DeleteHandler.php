<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Api\Handlers;

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Delete a User record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = UserResource::class;

    protected static string $permission = 'Delete:User';

    protected function beforeDelete(Model $record): void
    {
        if ($record->is(auth()->user())) {
            static::fail('id', __('You cannot delete your own account.'));
        }
    }
}
