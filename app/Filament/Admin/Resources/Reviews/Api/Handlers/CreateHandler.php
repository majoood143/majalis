<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Reviews\Api\Handlers;

use App\Filament\Admin\Resources\ReviewResource;
use App\Filament\Admin\Resources\Reviews\Api\Requests\CreateReviewRequest;
use App\Filament\Admin\Resources\Reviews\Api\Transformers\ReviewTransformer;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;

/**
 * Create a Review record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = ReviewResource::class;

    protected static string $permission = 'Create:Review';

    protected static string $transformer = ReviewTransformer::class;

    /**
     * Create Review
     */
    public function handler(CreateReviewRequest $request)
    {
        return $this->store($request);
    }
}
