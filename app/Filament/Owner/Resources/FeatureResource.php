<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\FeatureResource\Pages;
use App\Models\Hall;
use App\Models\HallFeature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * FeatureResource for Owner Panel
 *
 * Allows hall owners to:
 * - Browse available system features
 * - See which features they've added to their halls
 * - Quick-add features to halls
 * - Request new features from admin
 *
 * Note: Features are managed by admin. Owners can only view and assign.
 *
 * @package App\Filament\Owner\Resources
 */
class FeatureResource extends Resource
{
    /**
     * The model the resource corresponds to.
     */
    protected static ?string $model = HallFeature::class;

    /**
     * The navigation icon.
     */
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    /**
     * The navigation group.
     */
    protected static ?string $navigationGroup = 'Hall Management';

    /**
     * The navigation sort order.
     */
    protected static ?int $navigationSort = 5;

    /**
     * Get the navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('owner.features.navigation');
    }

    /**
     * Get the model label.
     */
    public static function getModelLabel(): string
    {
        return __('owner.features.singular');
    }

    /**
     * Get the plural model label.
     */
    public static function getPluralModelLabel(): string
    {
        return __('owner.features.plural');
    }

    /**
     * Get the navigation badge (total active features).
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) HallFeature::where('is_active', true)->count();
    }

    /**
     * Get the navigation badge color.
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    /**
     * Get the Eloquent query for active features only.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Owners cannot create features (admin only).
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Owners cannot edit features (admin only).
     */
    public static function canEdit($record): bool
    {
        return false;
    }

    /**
     * Owners cannot delete features (admin only).
     */
    public static function canDelete($record): bool
    {
        return false;
    }

    /**
     * Configure the table for listing features.
     */
    public static function table(Table $table): Table
    {
        $user = Auth::user();

        // Get owner's hall IDs and their features
        $ownerHallIds = Hall::where('owner_id', $user?->id)->pluck('id')->toArray();
        $ownerFeatureIds = Hall::whereIn('id', $ownerHallIds)
            ->pluck('features')
            ->flatten()
            ->unique()
            ->filter()
            ->toArray();

        return $table
            ->defaultSort('order', 'asc')
            ->striped()
            ->columns([
                // Icon Column
                Tables\Columns\TextColumn::make('icon')
                    ->label('')
                    ->formatStateUsing(fn (string $state): string => '')
                    ->html()
                    ->extraAttributes(fn ($record) => [
                        'class' => 'w-10',
                    ])
                    ->view('filament.owner.components.feature-icon'),

                // Feature Name
                Tables\Columns\TextColumn::make('name')
                    ->label(__('owner.features.columns.name'))
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', app()->getLocale()))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($q) use ($search) {
                            $q->whereRaw("LOWER(JSON_EXTRACT(name, '$.en')) LIKE ?", ['%' . strtolower($search) . '%'])
                                ->orWhereRaw("LOWER(JSON_EXTRACT(name, '$.ar')) LIKE ?", ['%' . strtolower($search) . '%']);
                        });
                    })
                    ->sortable(),

                // Description
                Tables\Columns\TextColumn::make('description')
                    ->label(__('owner.features.columns.description'))
                    ->formatStateUsing(fn ($record) => $record->getTranslation('description', app()->getLocale()) ?? '-')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),

                // Your Halls Count
                Tables\Columns\TextColumn::make('halls_count')
                    ->label(__('owner.features.columns.your_halls'))
                    ->state(function ($record) use ($ownerHallIds): int {
                        return Hall::whereIn('id', $ownerHallIds)
                            ->whereJsonContains('features', $record->id)
                            ->count();
                    })
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray')
                    ->icon(fn (int $state): string => $state > 0 ? 'heroicon-o-check' : 'heroicon-o-minus'),

                // Status Badge (added to your halls or not)
                Tables\Columns\IconColumn::make('is_added')
                    ->label(__('owner.features.columns.added'))
                    ->state(fn ($record) => in_array($record->id, $ownerFeatureIds))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->filters([
                // Added to My Halls filter
                Tables\Filters\TernaryFilter::make('added_to_halls')
                    ->label(__('owner.features.filters.added_status'))
                    ->placeholder(__('owner.features.filters.all'))
                    ->trueLabel(__('owner.features.filters.added_only'))
                    ->falseLabel(__('owner.features.filters.not_added'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereIn('id', $ownerFeatureIds),
                        false: fn (Builder $query) => $query->whereNotIn('id', $ownerFeatureIds),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->actions([
                // View Details
                Tables\Actions\ViewAction::make()
                    ->label(__('owner.features.actions.view'))
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn ($record) => $record->getTranslation('name', app()->getLocale())),

                // Quick Add to Hall
                Tables\Actions\Action::make('add_to_hall')
                    ->label(__('owner.features.actions.add_to_hall'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\CheckboxList::make('hall_ids')
                            ->label(__('owner.features.fields.select_halls'))
                            ->options(function () use ($user) {
                                return Hall::where('owner_id', $user?->id)
                                    ->get()
                                    ->mapWithKeys(fn ($hall) => [
                                        $hall->id => $hall->getTranslation('name', app()->getLocale())
                                    ]);
                            })
                            ->bulkToggleable()
                            ->columns(2)
                            ->required(),
                    ])
                    ->action(function (HallFeature $record, array $data): void {
                        $addedCount = 0;
                        $alreadyCount = 0;

                        foreach ($data['hall_ids'] as $hallId) {
                            $hall = Hall::find($hallId);

                            if (!$hall || $hall->owner_id !== Auth::id()) {
                                continue;
                            }

                            $features = $hall->features ?? [];

                            if (!in_array($record->id, $features)) {
                                $features[] = $record->id;
                                $hall->update(['features' => $features]);
                                $addedCount++;
                            } else {
                                $alreadyCount++;
                            }
                        }

                        if ($addedCount > 0) {
                            Notification::make()
                                ->success()
                                ->title(__('owner.features.notifications.added'))
                                ->body(__('owner.features.notifications.added_body', [
                                    'feature' => $record->getTranslation('name', app()->getLocale()),
                                    'count' => $addedCount,
                                ]))
                                ->send();
                        }

                        if ($alreadyCount > 0) {
                            Notification::make()
                                ->info()
                                ->title(__('owner.features.notifications.already_added'))
                                ->body(__('owner.features.notifications.already_added_body', [
                                    'count' => $alreadyCount,
                                ]))
                                ->send();
                        }
                    }),

                // Remove from Hall
                Tables\Actions\Action::make('remove_from_hall')
                    ->label(__('owner.features.actions.remove_from_hall'))
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->visible(fn ($record) => in_array($record->id, $ownerFeatureIds))
                    ->form([
                        Forms\Components\CheckboxList::make('hall_ids')
                            ->label(__('owner.features.fields.select_halls_remove'))
                            ->options(function ($record) use ($user) {
                                return Hall::where('owner_id', $user?->id)
                                    ->whereJsonContains('features', $record->id)
                                    ->get()
                                    ->mapWithKeys(fn ($hall) => [
                                        $hall->id => $hall->getTranslation('name', app()->getLocale())
                                    ]);
                            })
                            ->bulkToggleable()
                            ->columns(2)
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading(__('owner.features.actions.confirm_remove'))
                    ->action(function (HallFeature $record, array $data): void {
                        $removedCount = 0;

                        foreach ($data['hall_ids'] as $hallId) {
                            $hall = Hall::find($hallId);

                            if (!$hall || $hall->owner_id !== Auth::id()) {
                                continue;
                            }

                            $features = $hall->features ?? [];
                            $features = array_values(array_filter($features, fn ($f) => $f != $record->id));
                            $hall->update(['features' => $features]);
                            $removedCount++;
                        }

                        Notification::make()
                            ->success()
                            ->title(__('owner.features.notifications.removed'))
                            ->body(__('owner.features.notifications.removed_body', [
                                'feature' => $record->getTranslation('name', app()->getLocale()),
                                'count' => $removedCount,
                            ]))
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Bulk Add to Hall
                    Tables\Actions\BulkAction::make('bulk_add')
                        ->label(__('owner.features.bulk.add_to_hall'))
                        ->icon('heroicon-o-plus-circle')
                        ->color('success')
                        ->form([
                            Forms\Components\CheckboxList::make('hall_ids')
                                ->label(__('owner.features.fields.select_halls'))
                                ->options(function () use ($user) {
                                    return Hall::where('owner_id', $user?->id)
                                        ->get()
                                        ->mapWithKeys(fn ($hall) => [
                                            $hall->id => $hall->getTranslation('name', app()->getLocale())
                                        ]);
                                })
                                ->bulkToggleable()
                                ->columns(2)
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $totalAdded = 0;

                            foreach ($data['hall_ids'] as $hallId) {
                                $hall = Hall::find($hallId);

                                if (!$hall || $hall->owner_id !== Auth::id()) {
                                    continue;
                                }

                                $features = $hall->features ?? [];
                                $originalCount = count($features);

                                foreach ($records as $feature) {
                                    if (!in_array($feature->id, $features)) {
                                        $features[] = $feature->id;
                                    }
                                }

                                if (count($features) > $originalCount) {
                                    $hall->update(['features' => $features]);
                                    $totalAdded += count($features) - $originalCount;
                                }
                            }

                            Notification::make()
                                ->success()
                                ->title(__('owner.features.notifications.bulk_added'))
                                ->body(__('owner.features.notifications.bulk_added_body', [
                                    'count' => $totalAdded,
                                ]))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading(__('owner.features.empty.heading'))
            ->emptyStateDescription(__('owner.features.empty.description'))
            ->emptyStateIcon('heroicon-o-check-badge');
    }

    /**
     * Configure the infolist for viewing feature details.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        $user = Auth::user();

        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                // Feature Icon
                                Infolists\Components\TextEntry::make('icon')
                                    ->label(__('owner.features.fields.icon'))
                                    ->formatStateUsing(fn ($record) => view('filament.owner.components.feature-icon-large', ['record' => $record])->render())
                                    ->html(),

                                // Feature Name
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('owner.features.fields.name'))
                                    ->formatStateUsing(fn ($record) => 
                                        $record->getTranslation('name', 'en') . ' / ' . $record->getTranslation('name', 'ar')
                                    ),
                            ]),

                        // Description
                        Infolists\Components\TextEntry::make('description')
                            ->label(__('owner.features.fields.description'))
                            ->formatStateUsing(fn ($record) => 
                                $record->getTranslation('description', app()->getLocale()) ?? __('owner.features.no_description')
                            )
                            ->columnSpanFull(),

                        // Halls using this feature
                        Infolists\Components\TextEntry::make('your_halls')
                            ->label(__('owner.features.fields.your_halls_with'))
                            ->state(function ($record) use ($user) {
                                $halls = Hall::where('owner_id', $user?->id)
                                    ->whereJsonContains('features', $record->id)
                                    ->get();

                                if ($halls->isEmpty()) {
                                    return __('owner.features.not_added_yet');
                                }

                                return $halls->map(fn ($h) => $h->getTranslation('name', app()->getLocale()))->implode(', ');
                            })
                            ->badge()
                            ->color('success')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Get the pages for the resource.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeatures::route('/'),
            'manage' => Pages\ManageHallFeatures::route('/manage'),
        ];
    }
}
