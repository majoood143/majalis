<?php

namespace App\Filament\Admin\Resources\HallFeatureResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Illuminate\Support\Str;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\TextSize;
use Filament\Infolists\Components\IconEntry;
use Filament\Support\Enums\IconSize;
use Filament\Infolists\Components\ViewEntry;
use App\Models\HallFeature;
use Spatie\Activitylog\Models\Activity;
use App\Filament\Admin\Resources\HallFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ViewHallFeature extends ViewRecord
{
    protected static string $resource = HallFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),

            Action::make('toggleActive')
                ->label(fn() => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title('Status Updated')
                        ->send();

                    Cache::tags(['features'])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Action::make('viewHalls')
                ->label('View Halls')
                ->icon('heroicon-o-building-storefront')
                ->color('info')
                ->badge(fn() => $this->record->halls()->count())
                ->url(fn() => route('filament.admin.resources.halls.index', [
                    'tableFilters' => [
                        'features' => ['values' => [$this->record->id]]
                    ]
                ]))
                ->visible(fn() => $this->record->halls()->count() > 0),

            Action::make('duplicate')
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $newFeature = $this->record->replicate();

                    $name = $newFeature->getTranslations('name');
                    foreach ($name as $locale => $value) {
                        $name[$locale] = $value . ' (Copy)';
                    }
                    $newFeature->setTranslations('name', $name);

                    $newFeature->slug = Str::slug($newFeature->getTranslation('name', 'en')) . '-copy';
                    $newFeature->is_active = false;
                    $newFeature->save();

                    Notification::make()
                        ->success()
                        ->title('Feature Duplicated')
                        ->actions([
                            Action::make('view')
                                ->label('View Duplicate')
                                ->url(HallFeatureResource::getUrl('view', ['record' => $newFeature->id])),
                        ])
                        ->send();
                }),

            DeleteAction::make()
                ->before(function (DeleteAction $action) {
                    if ($this->record->halls()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Delete')
                            ->body('This feature is used by halls.')
                            ->persistent()
                            ->send();

                        $action->cancel();
                    }
                })
                ->successRedirectUrl(route('filament.admin.resources.hall-features.index')),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $infolist
            ->schema([
                Section::make('Feature Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Feature Name')
                                    ->formatStateUsing(fn($record) => $record->name)
                                    ->badge()
                                    ->color('primary')
                                    ->size(TextSize::Large)
                                    ->icon('heroicon-o-star'),

                                TextEntry::make('slug')
                                    ->label('Slug')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable()
                                    ->copyMessage('Slug copied!')
                                    ->icon('heroicon-o-link'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name.en')
                                    ->label('Name (English)')
                                    ->icon('heroicon-o-language'),

                                TextEntry::make('name.ar')
                                    ->label('Name (Arabic)')
                                    ->icon('heroicon-o-language'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('icon')
                                    ->label('Icon')
                                    ->placeholder('No icon set')
                                    ->badge()
                                    ->color('info')
                                    ->copyable()
                                    ->icon(fn($state) => $state ?: 'heroicon-o-photo'),

                                IconEntry::make('is_active')
                                    ->label('Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->size(IconSize::Large),
                            ]),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->collapsible(),

                Section::make('Description')
                    ->schema([
                        TextEntry::make('description.en')
                            ->label('Description (English)')
                            ->default('No description provided')
                            ->columnSpanFull(),

                        TextEntry::make('description.ar')
                            ->label('Description (Arabic)')
                            ->default('لا يوجد وصف')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->visible(fn($record) => $record->description),

                Section::make('Usage Statistics')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('halls_count')
                                    ->label('Total Halls')
                                    ->state(fn($record) => $record->halls()->count())
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-building-storefront'),

                                TextEntry::make('active_halls_count')
                                    ->label('Active Halls')
                                    ->state(fn($record) => $record->halls()->where('is_active', true)->count())
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-check-circle'),

                                TextEntry::make('order')
                                    ->label('Display Order')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-bars-3'),

                                TextEntry::make('popularity')
                                    ->label('Popularity')
                                    ->state(function ($record) {
                                        $count = $record->halls()->count();
                                        if ($count >= 20) return 'Very Popular';
                                        if ($count >= 10) return 'Popular';
                                        if ($count >= 5) return 'Moderate';
                                        if ($count >= 1) return 'Low';
                                        return 'Not Used';
                                    })
                                    ->badge()
                                    ->color(function ($record) {
                                        $count = $record->halls()->count();
                                        if ($count >= 20) return 'success';
                                        if ($count >= 10) return 'info';
                                        if ($count >= 5) return 'warning';
                                        if ($count >= 1) return 'gray';
                                        return 'danger';
                                    })
                                    ->icon('heroicon-o-fire'),
                            ]),
                    ])
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible(),

                Section::make('Halls Using This Feature')
                    ->schema([
                        ViewEntry::make('halls_list')
                            ->label('')
                            ->view('filament.infolists.components.halls-list', [
                                'halls' => fn($record) => $record->halls()
                                    ->with('city', 'region')
                                    ->orderBy('name')
                                    ->limit(10)
                                    ->get()
                            ]),

                        TextEntry::make('more_halls')
                            ->label('')
                            ->state(function ($record) {
                                $total = $record->halls()->count();
                                if ($total > 10) {
                                    return "... and " . ($total - 10) . " more halls";
                                }
                                return null;
                            })
                            ->color('info')
                            ->visible(fn($record) => $record->halls()->count() > 10),
                    ])
                    ->icon('heroicon-o-building-storefront')
                    ->visible(fn($record) => $record->halls()->count() > 0)
                    ->collapsible(),

                Section::make('Feature Analytics')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('cities_count')
                                    ->label('Cities Coverage')
                                    ->state(function ($record) {
                                        return $record->halls()
                                            ->distinct('city_id')
                                            ->count('city_id') . ' cities';
                                    })
                                    ->badge()
                                    ->color('purple')
                                    ->icon('heroicon-o-map-pin'),

                                TextEntry::make('regions_count')
                                    ->label('Regions Coverage')
                                    ->state(function ($record) {
                                        return $record->halls()
                                            ->distinct('region_id')
                                            ->count('region_id') . ' regions';
                                    })
                                    ->badge()
                                    ->color('purple')
                                    ->icon('heroicon-o-map'),

                                TextEntry::make('feature_rank')
                                    ->label('Feature Rank')
                                    ->state(function ($record) {
                                        $rank = HallFeature::withCount('halls')
                                            ->orderBy('halls_count', 'desc')
                                            ->pluck('id')
                                            ->search($record->id);

                                        return $rank !== false ? '#' . ($rank + 1) : 'N/A';
                                    })
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-trophy'),
                            ]),
                    ])
                    ->icon('heroicon-o-chart-pie')
                    ->collapsible(),

                Section::make('System Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('Feature ID')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag'),

                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('d M Y, h:i A')
                                    ->icon('heroicon-o-calendar'),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('d M Y, h:i A')
                                    ->since()
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->icon('heroicon-o-server')
                    ->collapsed(),

                Section::make('Activity History')
                    ->schema([
                        ViewEntry::make('activity_log')
                            ->label('')
                            ->view('filament.infolists.components.activity-log', [
                                'activities' => fn($record) => activity()
                                    ->forSubject($record)
                                    ->latest()
                                    ->limit(10)
                                    ->get()
                            ]),
                    ])
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->visible(fn() => class_exists(Activity::class)),
            ]);
    }

    public function getTitle(): string
    {
        return 'View Feature: ' . $this->record->name;
    }

    public function getSubheading(): ?string
    {
        $hallsCount = $this->record->halls()->count();
        $status = $this->record->is_active ? 'Active' : 'Inactive';
        $slug = $this->record->slug;

        return "{$status} • {$hallsCount} Hall(s) • Slug: {$slug}";
    }

    public function getBreadcrumb(): string
    {
        return $this->record->name;
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
