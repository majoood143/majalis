<?php

namespace App\Filament\Admin\Resources\HallFeatureResource\Pages;

use App\Filament\Admin\Resources\HallFeatureResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CreateHallFeature extends CreateRecord
{
    protected static string $resource = HallFeatureResource::class;

    protected static bool $canCreateAnother = true;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Hall Feature Created')
            ->body('The hall feature has been created successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;

        // Auto-generate slug if not provided
        if (empty($data['slug']) && isset($data['name']['en'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']['en']);
        }

        // Validate icon format
        if (!empty($data['icon']) && !$this->isValidHeroicon($data['icon'])) {
            Notification::make()
                ->warning()
                ->title('Invalid Icon Format')
                ->body('Icon should be in format: heroicon-o-icon-name')
                ->send();
        }

        // Check for duplicate feature names
        $exists = \App\Models\HallFeature::where(function ($query) use ($data) {
            $query->where('name->en', $data['name']['en'])
                ->orWhere('name->ar', $data['name']['ar']);
        })->exists();

        if ($exists) {
            Notification::make()
                ->warning()
                ->title('Similar Feature Found')
                ->body('A feature with a similar name already exists.')
                ->persistent()
                ->send();
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);

        // Log the creation
        activity()
            ->performedOn($record)
            ->causedBy(Auth::user())
            ->withProperties([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ])
            ->log('Hall feature created');

        return $record;
    }

    protected function afterCreate(): void
    {
        $feature = $this->record;

        // Log the creation
        Log::info('Hall feature created', [
            'feature_id' => $feature->id,
            'name' => $feature->name,
            'slug' => $feature->slug,
            'created_by' => Auth::id(),
        ]);

        // Clear cache
        Cache::tags(['features'])->flush();
    }

    protected function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $baseSlug = $slug;
        $counter = 1;

        while (\App\Models\HallFeature::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function isValidHeroicon(string $icon): bool
    {
        // Check if icon follows heroicon format
        return preg_match('/^heroicon-(o|s|m)-[a-z0-9-]+$/', $icon);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->submit(null)
                ->keyBindings(['mod+s']),

            $this->getCreateAnotherFormAction()
                ->keyBindings(['mod+shift+s']),

            $this->getCancelFormAction(),
        ];
    }

    public function getTitle(): string
    {
        return 'Create Hall Feature';
    }

    public function getSubheading(): ?string
    {
        return 'Add a new feature that can be assigned to halls';
    }
}
