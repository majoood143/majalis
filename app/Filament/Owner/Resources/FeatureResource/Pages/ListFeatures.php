<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\FeatureResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use App\Filament\Owner\Resources\FeatureResource;
use App\Models\Hall;
use App\Models\HallFeature;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * ListFeatures Page for Owner Panel
 *
 * Lists all available system features with tabs.
 */
class ListFeatures extends ListRecords
{
    /**
     * The resource this page belongs to.
     */
    protected static string $resource = FeatureResource::class;

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.features.title');
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return __('owner.features.heading');
    }

    /**
     * Get the page subheading.
     */
    public function getSubheading(): ?string
    {
        return __('owner.features.subheading');
    }

    /**
     * Get header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // Manage Hall Features
            Action::make('manage')
                ->label(__('owner.features.actions.manage_halls'))
                ->icon('heroicon-o-cog-6-tooth')
                ->color('primary')
                ->url(fn () => FeatureResource::getUrl('manage')),

            // Request New Feature
            Action::make('request')
                ->label(__('owner.features.actions.request_feature'))
                ->icon('heroicon-o-light-bulb')
                ->color('warning')
                ->schema([
                    TextInput::make('name_en')
                        ->label(__('owner.features.fields.feature_name_en'))
                        ->required()
                        ->maxLength(100)
                        ->placeholder('e.g., Swimming Pool'),

                    TextInput::make('name_ar')
                        ->label(__('owner.features.fields.feature_name_ar'))
                        ->required()
                        ->maxLength(100)
                        ->placeholder('مثال: مسبح'),

                    Textarea::make('description')
                        ->label(__('owner.features.fields.description'))
                        ->rows(3)
                        ->placeholder(__('owner.features.placeholders.describe_feature')),
                ])
                ->action(function (array $data): void {
                    // In a real app, this would create a feature request for admin review
                    // For now, just show a success notification

                    Notification::make()
                        ->success()
                        ->title(__('owner.features.notifications.request_sent'))
                        ->body(__('owner.features.notifications.request_sent_body'))
                        ->persistent()
                        ->send();

                    // Optional: Send email/notification to admin
                    // Mail::to(config('mail.admin_email'))->send(new FeatureRequestMail($data));
                }),
        ];
    }

    /**
     * Get tabs for filtering.
     *
     * @return array<\Filament\Schemas\Components\Tabs\Tab>
     */
    public function getTabs(): array
    {
        $user = Auth::user();
        $ownerHallIds = Hall::where('owner_id', $user?->id)->pluck('id')->toArray();

        // Get feature IDs that are assigned to owner's halls
        $ownerFeatureIds = Hall::whereIn('id', $ownerHallIds)
            ->pluck('features')
            ->flatten()
            ->unique()
            ->filter()
            ->toArray();

        return [
            'all' => \Filament\Schemas\Components\Tabs\Tab::make(__('owner.features.tabs.all'))
                ->badge(HallFeature::where('is_active', true)->count())
                ->badgeColor('primary'),

            'added' => \Filament\Schemas\Components\Tabs\Tab::make(__('owner.features.tabs.added'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('id', $ownerFeatureIds))
                ->badge(count($ownerFeatureIds))
                ->badgeColor('success')
                ->icon('heroicon-o-check-circle'),

            'not_added' => \Filament\Schemas\Components\Tabs\Tab::make(__('owner.features.tabs.not_added'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('id', $ownerFeatureIds))
                ->badge(HallFeature::where('is_active', true)->whereNotIn('id', $ownerFeatureIds)->count())
                ->badgeColor('gray')
                ->icon('heroicon-o-plus-circle'),

            'popular' => \Filament\Schemas\Components\Tabs\Tab::make(__('owner.features.tabs.popular'))
                ->modifyQueryUsing(function (Builder $query) {
                    // Get most commonly used features
                    $popularIds = [];
                    $allHalls = Hall::all();
                    $featureCounts = [];

                    foreach ($allHalls as $hall) {
                        $features = $hall->features ?? [];
                        foreach ($features as $fId) {
                            $featureCounts[$fId] = ($featureCounts[$fId] ?? 0) + 1;
                        }
                    }

                    arsort($featureCounts);
                    $popularIds = array_slice(array_keys($featureCounts), 0, 10);

                    return $query->whereIn('id', $popularIds);
                })
                ->icon('heroicon-o-fire'),
        ];
    }
}
