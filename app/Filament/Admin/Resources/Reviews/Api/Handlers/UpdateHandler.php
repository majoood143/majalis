<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Reviews\Api\Handlers;

use App\Filament\Admin\Resources\ReviewResource;
use App\Filament\Admin\Resources\Reviews\Api\Requests\UpdateReviewRequest;
use App\Filament\Admin\Resources\Reviews\Api\Transformers\ReviewTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;

/**
 * Update a Review record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = ReviewResource::class;

    protected static string $permission = 'Update:Review';

    protected static string $transformer = ReviewTransformer::class;

    /**
     * Update Review
     */
    public function handler(UpdateReviewRequest $request)
    {
        return $this->modify($request);
    }
}
