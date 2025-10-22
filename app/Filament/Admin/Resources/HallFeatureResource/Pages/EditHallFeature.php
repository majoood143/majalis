<?php

namespace App\Filament\Admin\Resources\HallFeatureResource\Pages;

use App\Filament\Admin\Resources\HallFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditHallFeature extends EditRecord
{
    protected static string $resource = HallFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('toggleActive')
                ->label(fn() => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_active ? 'Deactivate Feature' : 'Activate Feature')
                ->modalDescription(fn() => $this->record->is_active
                    ? 'This will deactivate the feature. It will still appear on halls that have it.'
                    : 'This will activate the feature and make it available for selection.')
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->title('Status Updated')
                        ->body('Feature status has been updated successfully.')
                        ->success()
                        ->send();

                    Cache::tags(['features'])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('viewHalls')
                ->label('View Halls')
                ->icon('heroicon-o-building-storefront')
                ->color('info')
                //->badge(fn() => $this->record->halls()->count())
                ->url(fn() => route('filament.admin.resources.halls.index', [
                    'tableFilters' => [
                        'features' => ['values' => [$this->record->id]]
                    ]
                    ])),
                //->visible(fn() => $this->record->halls()->count() > 0),

            Actions\Action::make('regenerateSlug')
                ->label('Regenerate Slug')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Regenerate Slug')
                ->modalDescription('This will regenerate the slug from the current English name.')
                ->action(function () {
                    $oldSlug = $this->record->slug;
                    $newSlug = $this->generateUniqueSlug($this->record->getTranslation('name', 'en'));

                    $this->record->slug = $newSlug;
                    $this->record->save();

                    activity()
                        ->performedOn($this->record)
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'old_slug' => $oldSlug,
                            'new_slug' => $newSlug,
                        ])
                        ->log('Slug regenerated');

                    Notification::make()
                        ->success()
                        ->title('Slug Regenerated')
                        ->body("Slug updated from '{$oldSlug}' to '{$newSlug}'")
                        ->send();
                }),

            Actions\Action::make('updateIcon')
                ->label('Update Icon')
                ->icon('heroicon-o-sparkles')
                ->color('purple')
                ->form([
                    \Filament\Forms\Components\TextInput::make('icon')
                        ->label('Icon')
                        ->placeholder('heroicon-o-icon-name')
                        ->helperText('Use format: heroicon-o-icon-name')
                        ->default(fn() => $this->record->icon),

                    \Filament\Forms\Components\Placeholder::make('icon_preview')
                        ->label('Common Icons')
                        ->content('wifi, parking, music, utensils, tv, air-conditioning, wheelchair-accessible'),
                ])
                ->action(function (array $data) {
                    if (!empty($data['icon']) && !$this->isValidHeroicon($data['icon'])) {
                        Notification::make()
                            ->danger()
                            ->title('Invalid Icon Format')
                            ->body('Icon should be in format: heroicon-o-icon-name')
                            ->persistent()
                            ->send();
                        return;
                    }

                    $this->record->icon = $data['icon'] ?: null;
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title('Icon Updated')
                        ->body('Feature icon has been updated.')
                        ->send();

                    Cache::tags(['features'])->flush();
                }),

            Actions\Action::make('duplicate')
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Duplicate Feature')
                ->modalDescription('This will create a copy of this feature.')
                ->action(function () {
                    $newFeature = $this->record->replicate();

                    // Update name to indicate it's a copy
                    $name = $newFeature->getTranslations('name');
                    foreach ($name as $locale => $value) {
                        $name[$locale] = $value . ' (Copy)';
                    }
                    $newFeature->setTranslations('name', $name);

                    // Generate new unique slug
                    $newFeature->slug = $this->generateUniqueSlug($newFeature->getTranslation('name', 'en'));
                    $newFeature->is_active = false;

                    $newFeature->save();

                    Notification::make()
                        ->success()
                        ->title('Feature Duplicated')
                        ->body('The feature has been duplicated successfully.')
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('Edit Duplicate')
                                ->url(HallFeatureResource::getUrl('edit', ['record' => $newFeature->id])),
                        ])
                        ->send();
                }),

            Actions\Action::make('reorderUp')
                ->label('Move Up')
                ->icon('heroicon-o-arrow-up')
                ->color('gray')
                ->visible(fn() => $this->canMoveUp())
                ->action(function () {
                    $this->moveFeature('up');
                }),

            Actions\Action::make('reorderDown')
                ->label('Move Down')
                ->icon('heroicon-o-arrow-down')
                ->color('gray')
                ->visible(fn() => $this->canMoveDown())
                ->action(function () {
                    $this->moveFeature('down');
                }),

            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    $hallsCount = $this->record->halls()->count();

                    if ($hallsCount > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Delete Feature')
                            ->body("This feature is used by {$hallsCount} hall(s). Please remove it from halls first.")
                            ->persistent()
                            ->send();

                        $action->cancel();
                    }
                })
                ->after(function () {
                    Cache::tags(['features'])->flush();
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Feature Deleted')
                        ->body('The hall feature has been deleted successfully.')
                ),

            Actions\Action::make('viewHistory')
                ->label('View History')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->modalContent(fn() => view('filament.pages.activity-log', [
                    'activities' => activity()
                        ->forSubject($this->record)
                        ->latest()
                        ->get()
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Feature Updated')
            ->body('The hall feature has been updated successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Auto-generate slug if empty
        if (empty($data['slug']) && isset($data['name']['en'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']['en'], $this->record->id);
        }

        // Validate icon format
        if (!empty($data['icon']) && !$this->isValidHeroicon($data['icon'])) {
            Notification::make()
                ->warning()
                ->title('Invalid Icon Format')
                ->body('Icon should be in format: heroicon-o-icon-name')
                ->send();
        }

        // Check for duplicate slug
        if ($data['slug'] !== $this->record->slug) {
            $exists = \App\Models\HallFeature::where('slug', $data['slug'])
                ->where('id', '!=', $this->record->id)
                ->exists();

            if ($exists) {
                Notification::make()
                    ->danger()
                    ->title('Duplicate Slug')
                    ->body('This slug is already used by another feature.')
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $oldValues = $record->toArray();

        $record->update($data);

        $changes = array_diff_assoc($data, $oldValues);

        // Log the update
        activity()
            ->performedOn($record)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'changes' => $changes,
            ])
            ->log('Hall feature updated');

        return $record;
    }

    protected function afterSave(): void
    {
        // Clear cache
        Cache::tags(['features'])->flush();

        // Log the update
        Log::info('Hall feature updated', [
            'feature_id' => $this->record->id,
            'name' => $this->record->name,
            'updated_by' => Auth::id(),
        ]);
    }

    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $baseSlug = $slug;
        $counter = 1;

        $query = \App\Models\HallFeature::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;

            $query = \App\Models\HallFeature::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }

    protected function isValidHeroicon(string $icon): bool
    {
        return preg_match('/^heroicon-(o|s|m)-[a-z0-9-]+$/', $icon);
    }

    protected function canMoveUp(): bool
    {
        return \App\Models\HallFeature::where('order', '<', $this->record->order)->exists();
    }

    protected function canMoveDown(): bool
    {
        return \App\Models\HallFeature::where('order', '>', $this->record->order)->exists();
    }

    protected function moveFeature(string $direction): void
    {
        if ($direction === 'up') {
            $swapFeature = \App\Models\HallFeature::where('order', '<', $this->record->order)
                ->orderBy('order', 'desc')
                ->first();
        } else {
            $swapFeature = \App\Models\HallFeature::where('order', '>', $this->record->order)
                ->orderBy('order', 'asc')
                ->first();
        }

        if ($swapFeature) {
            $currentOrder = $this->record->order;
            $swapOrder = $swapFeature->order;

            $this->record->order = $swapOrder;
            $swapFeature->order = $currentOrder;

            $this->record->save();
            $swapFeature->save();

            Notification::make()
                ->success()
                ->title('Feature Reordered')
                ->body('Feature has been moved ' . $direction . '.')
                ->send();

            Cache::tags(['features'])->flush();
            $this->redirect(static::getUrl(['record' => $this->record]));
        }
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->submit(null)
                ->keyBindings(['mod+s']),

            $this->getCancelFormAction(),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit Feature: ' . $this->record->name;
    }

    public function getSubheading(): ?string
    {
        //$hallsCount = $this->record->halls()->count();
        $status = $this->record->is_active ? 'Active' : 'Inactive';

        //return "{$status} • Used by {$hallsCount} hall(s) • Order: {$this->record->order}";
        return "{$status} • Used by  hall(s) • Order: {$this->record->order}";
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
