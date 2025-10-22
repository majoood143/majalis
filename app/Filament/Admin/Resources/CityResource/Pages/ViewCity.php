<?php

namespace App\Filament\Admin\Resources\CityResource\Pages;

use App\Filament\Admin\Resources\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;

class ViewCity extends ViewRecord
{
    protected static string $resource = CityResource::class;

    protected static string $view = 'filament.resources.city-resource.pages.view-city';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),
            
            Actions\Action::make('toggleActive')
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
            
            Actions\Action::make('viewHalls')
                ->label('View Halls')
                ->icon('heroicon-o-building-storefront')
                ->color('info')
                ->url(fn () => route('filament.admin.resources.halls.index', [
                    'tableFilters' => [
                        'city_id' => ['value' => $this->record->id]
                    ]
                ]))
                ->visible(fn () => $this->record->halls()->count() > 0),
            
            Actions\Action::make('viewOnMap')
                ->label('View on Map')
                ->icon('heroicon-o-map-pin')
                ->color('success')
                ->url(fn () => "https://www.google.com/maps/search/?api=1&query={$this->record->latitude},{$this->record->longitude}")
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->latitude && $this->record->longitude),
            
            Actions\Action::make('duplicate')
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
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('View Duplicate')
                                ->url(CityResource::getUrl('view', ['record' => $newCity->id])),
                        ])
                        ->send();
                }),
            
            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
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

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('City Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Name')
                                    ->formatStateUsing(fn ($record) => $record->name)
                                    ->badge()
                                    ->color('primary')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                                
                                Infolists\Components\TextEntry::make('code')
                                    ->label('City Code')
                                    ->badge()
                                    ->color('info')
                                    ->copyable()
                                    ->copyMessage('Code copied!')
                                    ->copyMessageDuration(1500),
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
                        
                        Infolists\Components\Grid::make(1)
                            ->schema([
                                Infolists\Components\TextEntry::make('description.en')
                                    ->label('Description (English)')
                                    ->default('No description provided')
                                    ->columnSpanFull(),
                                
                                Infolists\Components\TextEntry::make('description.ar')
                                    ->label('Description (Arabic)')
                                    ->default('لا يوجد وصف')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->collapsible(),
                
                Infolists\Components\Section::make('Location & Region')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('region.name')
                                    ->label('Region')
                                    ->formatStateUsing(fn ($record) => $record->region->name ?? 'N/A')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-map'),
                                
                                Infolists\Components\TextEntry::make('latitude')
                                    ->label('Latitude')
                                    ->default('Not set')
                                    ->icon('heroicon-o-globe-alt')
                                    ->copyable(),
                                
                                Infolists\Components\TextEntry::make('longitude')
                                    ->label('Longitude')
                                    ->default('Not set')
                                    ->icon('heroicon-o-globe-alt')
                                    ->copyable(),
                            ]),
                    ])
                    ->icon('heroicon-o-map-pin')
                    ->collapsible(),
                
                Infolists\Components\Section::make('Statistics')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('halls_count')
                                    ->label('Total Halls')
                                    ->state(fn ($record) => $record->halls()->count())
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-building-storefront'),
                                
                                Infolists\Components\TextEntry::make('active_halls_count')
                                    ->label('Active Halls')
                                    ->state(fn ($record) => $record->halls()->where('is_active', true)->count())
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-check-circle'),
                                
                                Infolists\Components\TextEntry::make('order')
                                    ->label('Display Order')
                                    ->badge()
                                    ->color('gray'),
                                
                                Infolists\Components\IconEntry::make('is_active')
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
                
                Infolists\Components\Section::make('System Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('id')
                                    ->label('City ID')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable(),
                                
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
                
                Infolists\Components\Section::make('Recent Activity')
                    ->schema([
                        Infolists\Components\ViewEntry::make('activity_log')
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
                    ->visible(fn () => class_exists(\Spatie\Activitylog\Models\Activity::class)),
            ]);
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