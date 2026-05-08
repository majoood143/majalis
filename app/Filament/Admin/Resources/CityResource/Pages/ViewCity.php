<?php

namespace App\Filament\Admin\Resources\CityResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\TextSize;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ViewEntry;
use Spatie\Activitylog\Models\Activity;
use App\Filament\Admin\Resources\CityResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewCity extends ViewRecord
{
    protected static string $resource = CityResource::class;


    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),

            Action::make('toggleActive')
                ->label(fn () => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn () => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn () => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn () => $this->record->is_active ? 'Deactivate City' : 'Activate City')
                ->modalDescription(fn () => $this->record->is_active
                    ? 'Are you sure you want to deactivate this city?'
                    : 'Are you sure you want to activate this city?')
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->title('Status Updated')
                        ->body('City status has been updated successfully.')
                        ->success()
                        ->send();

                    // Refresh the page to show updated status
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Action::make('viewHalls')
                ->label('View Halls')
                ->icon('heroicon-o-building-storefront')
                ->color('info')
                ->url(fn () => route('filament.admin.resources.halls.index', [
                    'tableFilters' => [
                        'city_id' => ['value' => $this->record->id]
                    ]
                ]))
                ->visible(fn () => $this->record->halls()->count() > 0),

            Action::make('viewOnMap')
                ->label('View on Map')
                ->icon('heroicon-o-map-pin')
                ->color('success')
                ->url(fn () => "https://www.google.com/maps/search/?api=1&query={$this->record->latitude},{$this->record->longitude}")
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->latitude && $this->record->longitude),

            Action::make('duplicate')
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Duplicate City')
                ->modalDescription('This will create a copy of this city with a new code.')
                ->action(function () {
                    $newCity = $this->record->replicate();
                    $newCity->code = $this->record->code . '_COPY';
                    $newCity->is_active = false;
                    $newCity->save();

                    Notification::make()
                        ->success()
                        ->title('City Duplicated')
                        ->body('The city has been duplicated successfully.')
                        ->actions([
                            Action::make('view')
                                ->label('View Duplicate')
                                ->url(CityResource::getUrl('view', ['record' => $newCity->id])),
                        ])
                        ->send();
                }),

            DeleteAction::make()
                ->before(function (DeleteAction $action) {
                    if ($this->record->halls()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Delete City')
                            ->body('This city has ' . $this->record->halls()->count() . ' hall(s). Please remove or reassign them first.')
                            ->persistent()
                            ->send();

                        $action->cancel();
                    }
                })
                ->successRedirectUrl(route('filament.admin.resources.cities.index')),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('City Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Name')
                                    ->formatStateUsing(fn ($record) => $record->name)
                                    ->badge()
                                    ->color('primary')
                                    ->size(TextSize::Large),

                                TextEntry::make('code')
                                    ->label('City Code')
                                    ->badge()
                                    ->color('info')
                                    ->copyable()
                                    ->copyMessage('Code copied!')
                                    ->copyMessageDuration(1500),
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

                        Grid::make(1)
                            ->schema([
                                TextEntry::make('description.en')
                                    ->label('Description (English)')
                                    ->placeholder('No description provided')
                                    ->columnSpanFull(),

                                TextEntry::make('description.ar')
                                    ->label('Description (Arabic)')
                                    ->placeholder('لا يوجد وصف')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->collapsible(),

                Section::make('Location & Region')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('region.name')
                                    ->label('Region')
                                    ->formatStateUsing(fn ($record) => $record->region->name ?? 'N/A')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-map'),

                                TextEntry::make('latitude')
                                    ->label('Latitude')
                                    ->placeholder('Not set')
                                    ->icon('heroicon-o-globe-alt')
                                    ->copyable(),

                                TextEntry::make('longitude')
                                    ->label('Longitude')
                                    ->placeholder('Not set')
                                    ->icon('heroicon-o-globe-alt')
                                    ->copyable(),
                            ]),
                    ])
                    ->icon('heroicon-o-map-pin')
                    ->collapsible(),

                Section::make('Statistics')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('halls_count')
                                    ->label('Total Halls')
                                    ->state(fn ($record) => $record->halls()->count())
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-building-storefront'),

                                TextEntry::make('active_halls_count')
                                    ->label('Active Halls')
                                    ->state(fn ($record) => $record->halls()->where('is_active', true)->count())
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-check-circle'),

                                TextEntry::make('order')
                                    ->label('Display Order')
                                    ->badge()
                                    ->color('gray'),

                                IconEntry::make('is_active')
                                    ->label('Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ]),
                    ])
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible(),

                Section::make('System Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('City ID')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable(),

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

                Section::make('Recent Activity')
                    ->schema([
                        ViewEntry::make('activity_log')
                            ->label('')
                            ->view('filament.infolists.components.activity-log', [
                                'activities' => fn ($record) => activity()
                                    ->forSubject($record)
                                    ->latest()
                                    ->limit(5)
                                    ->get()
                            ]),
                    ])
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->visible(fn () => class_exists(Activity::class)),
            ])
            ->columns(1);
    }

    public function getTitle(): string
    {
        return 'View City: ' . $this->record->name;
    }

    public function getSubheading(): ?string
    {
        $hallsCount = $this->record->halls()->count();
        $status = $this->record->is_active ? 'Active' : 'Inactive';
        $region = $this->record->region->name ?? 'No Region';

        return "{$status} • {$region} • {$hallsCount} Hall(s)";
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
