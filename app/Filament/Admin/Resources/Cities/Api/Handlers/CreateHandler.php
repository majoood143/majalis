<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Cities\Api\Handlers;

use App\Filament\Admin\Resources\Cities\Api\Requests\CreateCityRequest;
use App\Filament\Admin\Resources\Cities\Api\Transformers\CityTransformer;
use App\Filament\Admin\Resources\CityResource;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use App\Models\City;
use Illuminate\Database\Eloquent\Model;

/**
 * Create a City record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = CityResource::class;

    protected static string $permission = 'Create:City';

    protected static string $transformer = CityTransformer::class;

    /**
     * Create City
     */
    public function handler(CreateCityRequest $request)
    {
        return $this->store($request);
    }

    protected function beforeCreate(array $data): array
    {
        if (empty($data['code']) && ! empty($data['name']['en'])) {
            // Same as the panel: uppercase name, spaces → _, max 10 chars, numbered if taken
            $base = substr(strtoupper(str_replace(' ', '_', $data['name']['en'])), 0, 10);
            $code = $base;
            $counter = 1;

            while (City::where('code', $code)->exists()) {
                $code = substr($base, 0, 10 - strlen((string) $counter)) . $counter++;
            }

            $data['code'] = $code;
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
