<?php

namespace App\Filament\Admin\Resources\HallFeatureResource\Pages;

use App\Filament\Admin\Resources\HallFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
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
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),

            Actions\Action::make('toggleActive')
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

            Actions\Action::make('viewHalls')
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

            Actions\Action::make('duplicate')
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

                    $newFeature->slug = \Illuminate\Support\Str::slug($newFeature->getTranslation('name', 'en')) . '-copy';
                    $newFeature->is_active = false;
                    $newFeature->save();

                    Notification::make()
                        ->success()
                        ->title('Feature Duplicated')
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('View Duplicate')
                                ->url(HallFeatureResource::getUrl('view', ['record' => $newFeature->id])),
                        ])
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
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

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Feature Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Feature Name')
                                    ->formatStateUsing(fn($record) => $record->name)
                                    ->badge()
                                    ->color('primary')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->icon('heroicon-o-star'),

                                Infolists\Components\TextEntry::make('slug')
                                    ->label('Slug')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable()
                                    ->copyMessage('Slug copied!')
                                    ->icon('heroicon-o-link'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name.en')
                                    ->label('Name (English)')
                                    ->icon('heroicon-o-language'),

                                Infolists\Components\TextEntry::make('name.ar')
                                    ->label('Name (Arabic)')
                                    ->icon('heroicon-o-language'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('icon')
                                    ->label('Icon')
                                    ->placeholder('No icon set')
                                    ->badge()
                                    ->color('info')
                                    ->copyable()
                                    ->icon(fn($state) => $state ?: 'heroicon-o-photo'),

                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->size(Infolists\Components\IconEntry\IconEntrySize::Large),
                            ]),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->collapsible(),

                Infolists\Components\Section::make('Description')
                    ->schema([
                        Infolists\Components\TextEntry::make('description.en')
                            ->label('Description (English)')
                            ->default('No description provided')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('description.ar')
                            ->label('Description (Arabic)')
                            ->default('لا يوجد وصف')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->visible(fn($record) => $record->description),

                Infolists\Components\Section::make('Usage Statistics')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('halls_count')
                                    ->label('Total Halls')
                                    ->state(fn($record) => $record->halls()->count())
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-building-storefront'),

                                Infolists\Components\TextEntry::make('active_halls_count')
                                    ->label('Active Halls')
                                    ->state(fn($record) => $record->halls()->where('is_active', true)->count())
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-check-circle'),

                                Infolists\Components\TextEntry::make('order')
                                    ->label('Display Order')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-bars-3'),

                                Infolists\Components\TextEntry::make('popularity')
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

                Infolists\Components\Section::make('Halls Using This Feature')
                    ->schema([
                        Infolists\Components\ViewEntry::make('halls_list')
                            ->label('')
                            ->view('filament.infolists.components.halls-list', [
                                'halls' => fn($record) => $record->halls()
                                    ->with('city', 'region')
                                    ->orderBy('name')
                                    ->limit(10)
                                    ->get()
                            ]),

                        Infolists\Components\TextEntry::make('more_halls')
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

                Infolists\Components\Section::make('Feature Analytics')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('cities_count')
                                    ->label('Cities Coverage')
                                    ->state(function ($record) {
                                        return $record->halls()
                                            ->distinct('city_id')
                                            ->count('city_id') . ' cities';
                                    })
                                    ->badge()
                                    ->color('purple')
                                    ->icon('heroicon-o-map-pin'),

                                Infolists\Components\TextEntry::make('regions_count')
                                    ->label('Regions Coverage')
                                    ->state(function ($record) {
                                        return $record->halls()
                                            ->distinct('region_id')
                                            ->count('region_id') . ' regions';
                                    })
                                    ->badge()
                                    ->color('purple')
                                    ->icon('heroicon-o-map'),

                                Infolists\Components\TextEntry::make('feature_rank')
                                    ->label('Feature Rank')
                                    ->state(function ($record) {
                                        $rank = \App\Models\HallFeature::withCount('halls')
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

                Infolists\Components\Section::make('System Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('id')
                                    ->label('Feature ID')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('d M Y, h:i A')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('d M Y, h:i A')
                                    ->since()
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->icon('heroicon-o-server')
                    ->collapsed(),

                Infolists\Components\Section::make('Activity History')
                    ->schema([
                        Infolists\Components\ViewEntry::make('activity_log')
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
                    ->visible(fn() => class_exists(\Spatie\Activitylog\Models\Activity::class)),
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
