<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\FeatureResource\Pages;

use App\Filament\Owner\Resources\FeatureResource;
use App\Models\Hall;
use App\Models\HallFeature;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Filament\Notifications\Notification;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * ManageHallFeatures Page for Owner Panel
 *
 * Visual interface for managing features across all owner's halls.
 * Shows a matrix of halls × features for easy bulk management.
 */
class ManageHallFeatures extends Page
{
    /**
     * The resource this page belongs to.
     */
    protected static string $resource = FeatureResource::class;

    /**
     * The view for this page.
     */
    protected static string $view = 'filament.owner.resources.feature-resource.pages.manage-hall-features';

    /**
     * Selected hall ID for single-hall mode.
     */
    public ?int $selectedHallId = null;

    /**
     * View mode: 'matrix' or 'single'
     */
    public string $viewMode = 'single';

    /**
     * Feature category filter.
     */
    public ?string $categoryFilter = null;

    /**
     * Mount the page.
     */
    public function mount(): void
    {
        $halls = $this->getOwnerHalls();

        // Pre-select first hall if only one
        if ($halls->count() === 1) {
            $this->selectedHallId = $halls->first()->id;
        } elseif ($halls->count() > 0) {
            $this->selectedHallId = $halls->first()->id;
        }
    }

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.features.manage.title');
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return __('owner.features.manage.heading');
    }

    /**
     * Get the subheading.
     */
    public function getSubheading(): ?string
    {
        return __('owner.features.manage.subheading');
    }

    /**
     * Get header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label(__('owner.features.actions.back_to_list'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => FeatureResource::getUrl('index')),

            Actions\Action::make('save_all')
                ->label(__('owner.features.actions.save_changes'))
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn () => $this->viewMode === 'matrix')
                ->action(fn () => $this->saveAllChanges()),
        ];
    }

    /**
     * Get owner's halls.
     */
    #[Computed]
    public function getOwnerHalls(): Collection
    {
        $user = Auth::user();

        return Hall::where('owner_id', $user?->id)
            ->where('is_active', true)
            ->orderBy('name->en')
            ->get();
    }

    /**
     * Get the selected hall.
     */
    #[Computed]
    public function selectedHall(): ?Hall
    {
        if (!$this->selectedHallId) {
            return null;
        }

        return Hall::find($this->selectedHallId);
    }

    /**
     * Get all active features grouped by category (if applicable).
     */
    #[Computed]
    public function getAllFeatures(): Collection
    {
        return HallFeature::where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get features for the selected hall.
     */
    #[Computed]
    public function hallFeatures(): array
    {
        if (!$this->selectedHall) {
            return [];
        }

        return $this->selectedHall->features ?? [];
    }

    /**
     * Get feature matrix data for matrix view.
     */
    #[Computed]
    public function featureMatrix(): array
    {
        $halls = $this->getOwnerHalls();
        $features = $this->getAllFeatures();
        $matrix = [];

        foreach ($halls as $hall) {
            $hallFeatures = $hall->features ?? [];
            $matrix[$hall->id] = [
                'hall' => $hall,
                'features' => [],
            ];

            foreach ($features as $feature) {
                $matrix[$hall->id]['features'][$feature->id] = in_array($feature->id, $hallFeatures);
            }
        }

        return $matrix;
    }

    /**
     * Get feature statistics.
     */
    #[Computed]
    public function featureStats(): array
    {
        $halls = $this->getOwnerHalls();
        $features = $this->getAllFeatures();

        $totalFeatures = $features->count();
        $usedFeatures = 0;
        $featureCounts = [];

        foreach ($features as $feature) {
            $count = 0;
            foreach ($halls as $hall) {
                $hallFeatures = $hall->features ?? [];
                if (in_array($feature->id, $hallFeatures)) {
                    $count++;
                }
            }
            $featureCounts[$feature->id] = $count;
            if ($count > 0) {
                $usedFeatures++;
            }
        }

        return [
            'total' => $totalFeatures,
            'used' => $usedFeatures,
            'unused' => $totalFeatures - $usedFeatures,
            'counts' => $featureCounts,
        ];
    }

    /**
     * Set selected hall.
     */
    public function setHall(int $hallId): void
    {
        $this->selectedHallId = $hallId;
        unset($this->selectedHall);
        unset($this->hallFeatures);
    }

    /**
     * Toggle view mode.
     */
    public function toggleViewMode(): void
    {
        $this->viewMode = $this->viewMode === 'single' ? 'matrix' : 'single';
    }

    /**
     * Toggle a feature for the selected hall.
     */
    public function toggleFeature(int $featureId): void
    {
        if (!$this->selectedHall) {
            return;
        }

        // Verify ownership
        if ($this->selectedHall->owner_id !== Auth::id()) {
            Notification::make()
                ->danger()
                ->title(__('owner.errors.unauthorized'))
                ->send();
            return;
        }

        $features = $this->selectedHall->features ?? [];
        $feature = HallFeature::find($featureId);

        if (in_array($featureId, $features)) {
            // Remove feature
            $features = array_values(array_filter($features, fn ($f) => $f != $featureId));
            $message = __('owner.features.notifications.feature_removed', [
                'feature' => $feature?->getTranslation('name', app()->getLocale()),
            ]);
        } else {
            // Add feature
            $features[] = $featureId;
            $message = __('owner.features.notifications.feature_added', [
                'feature' => $feature?->getTranslation('name', app()->getLocale()),
            ]);
        }

        $this->selectedHall->update(['features' => $features]);

        // Reset computed properties
        unset($this->hallFeatures);
        unset($this->featureStats);

        Notification::make()
            ->success()
            ->title($message)
            ->duration(2000)
            ->send();
    }

    /**
     * Toggle feature in matrix view.
     */
    public function toggleMatrixFeature(int $hallId, int $featureId): void
    {
        $hall = Hall::find($hallId);

        if (!$hall || $hall->owner_id !== Auth::id()) {
            return;
        }

        $features = $hall->features ?? [];

        if (in_array($featureId, $features)) {
            $features = array_values(array_filter($features, fn ($f) => $f != $featureId));
        } else {
            $features[] = $featureId;
        }

        $hall->update(['features' => $features]);

        // Reset computed
        unset($this->featureMatrix);
        unset($this->featureStats);
    }

    /**
     * Add all features to selected hall.
     */
    public function addAllFeatures(): void
    {
        if (!$this->selectedHall) {
            return;
        }

        $allFeatureIds = $this->getAllFeatures()->pluck('id')->toArray();
        $this->selectedHall->update(['features' => $allFeatureIds]);

        unset($this->hallFeatures);
        unset($this->featureStats);

        Notification::make()
            ->success()
            ->title(__('owner.features.notifications.all_added'))
            ->send();
    }

    /**
     * Remove all features from selected hall.
     */
    public function removeAllFeatures(): void
    {
        if (!$this->selectedHall) {
            return;
        }

        $this->selectedHall->update(['features' => []]);

        unset($this->hallFeatures);
        unset($this->featureStats);

        Notification::make()
            ->success()
            ->title(__('owner.features.notifications.all_removed'))
            ->send();
    }

    /**
     * Copy features from one hall to another.
     */
    public function copyFeaturesFrom(int $sourceHallId): void
    {
        if (!$this->selectedHall) {
            return;
        }

        $sourceHall = Hall::find($sourceHallId);

        if (!$sourceHall || $sourceHall->owner_id !== Auth::id()) {
            return;
        }

        $this->selectedHall->update(['features' => $sourceHall->features ?? []]);

        unset($this->hallFeatures);
        unset($this->featureStats);

        Notification::make()
            ->success()
            ->title(__('owner.features.notifications.copied'))
            ->body(__('owner.features.notifications.copied_body', [
                'source' => $sourceHall->getTranslation('name', app()->getLocale()),
            ]))
            ->send();
    }

    /**
     * Save all changes in matrix mode (batch update).
     */
    public function saveAllChanges(): void
    {
        Notification::make()
            ->success()
            ->title(__('owner.features.notifications.all_saved'))
            ->send();
    }
}
