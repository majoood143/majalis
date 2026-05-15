<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use App\Filament\Admin\Resources\ReviewResource\Pages\ListReviews;
use App\Filament\Admin\Resources\ReviewResource\Pages\CreateReview;
use App\Filament\Admin\Resources\ReviewResource\Pages\ViewReview;
use App\Filament\Admin\Resources\ReviewResource\Pages\EditReview;
use App\Filament\Admin\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string | \UnitEnum | null $navigationGroup = 'Booking Management';

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('review.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('review.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('review.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('review.sections.review_information'))
                    ->schema([
                        Select::make('hall_id')
                            ->relationship('hall', 'name')
                            ->label(__('review.fields.hall'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(),

                        Select::make('booking_id')
                            ->relationship('booking', 'booking_number')
                            ->label(__('review.fields.booking'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(),

                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label(__('review.fields.user'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(),

                        Select::make('rating')
                            ->label(__('review.fields.rating'))
                            ->options([
                                1 => __('review.ratings.1_star'),
                                2 => __('review.ratings.2_stars'),
                                3 => __('review.ratings.3_stars'),
                                4 => __('review.ratings.4_stars'),
                                5 => __('review.ratings.5_stars'),
                            ])
                            ->required(),

                        Textarea::make('comment')
                            ->label(__('review.fields.comment'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make(__('review.sections.detailed_ratings'))
                    ->schema([
                        Select::make('cleanliness_rating')
                            ->label(__('review.fields.cleanliness_rating'))
                            ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),

                        Select::make('service_rating')
                            ->label(__('review.fields.service_rating'))
                            ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),

                        Select::make('value_rating')
                            ->label(__('review.fields.value_rating'))
                            ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),

                        Select::make('location_rating')
                            ->label(__('review.fields.location_rating'))
                            ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),
                    ])->columns(4)
                    ->collapsible(),

                Section::make(__('review.sections.moderation'))
                    ->schema([
                        Toggle::make('is_approved')
                            ->label(__('review.fields.is_approved'))
                            ->inline(false),

                        Toggle::make('is_featured')
                            ->label(__('review.fields.is_featured'))
                            ->inline(false),

                        Toggle::make('is_late_review')
                            ->label(__('review.columns_extra.is_late_review'))
                            ->inline(false)
                            ->disabled(),

                        Toggle::make('marketing_consent')
                            ->label(__('review.columns_extra.marketing_consent'))
                            ->inline(false)
                            ->disabled(),

                        Textarea::make('admin_notes')
                            ->label(__('review.fields.admin_notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make(__('review.sections.owner_response'))
                    ->schema([
                        Textarea::make('owner_response')
                            ->label(__('review.fields.owner_response'))
                            ->rows(4)
                            ->columnSpanFull()
                            ->disabled(),

                        DateTimePicker::make('owner_response_at')
                            ->label(__('review.fields.owner_response_at'))
                            ->disabled(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hall.name')
                    ->label(__('review.columns.hall'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->hall->name),

                TextColumn::make('user.name')
                    ->label(__('review.columns.user'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rating')
                    ->label(__('review.columns.rating'))
                    ->badge()
                    ->sortable()
                    ->color(fn(int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn(int $state): string => str_repeat('⭐', $state)),

                TextColumn::make('comment')
                    ->label(__('review.columns.comment'))
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),

                IconColumn::make('is_approved')
                    ->label(__('review.columns.is_approved'))
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label(__('review.columns.is_featured'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('owner_response')
                    ->label(__('review.columns.owner_response'))
                    ->limit(30)
                    ->toggleable()
                    ->placeholder(__('review.placeholder.no_response')),

                IconColumn::make('is_late_review')
                    ->label(__('review.columns_extra.is_late_review'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('marketing_consent')
                    ->label(__('review.columns_extra.marketing_consent'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('review.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('hall_id')
                    ->relationship('hall', 'name')
                    ->label(__('review.filters.hall'))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('rating')
                    ->label(__('review.filters.rating'))
                    ->options([
                        1 => __('review.ratings.1_star'),
                        2 => __('review.ratings.2_stars'),
                        3 => __('review.ratings.3_stars'),
                        4 => __('review.ratings.4_stars'),
                        5 => __('review.ratings.5_stars'),
                    ]),

                TernaryFilter::make('is_approved')
                    ->label(__('review.filters.approved'))
                    ->boolean(),

                TernaryFilter::make('is_featured')
                    ->label(__('review.filters.featured'))
                    ->boolean(),

                TernaryFilter::make('owner_response')
                    ->label(__('review.filters.has_owner_response'))
                    ->queries(
                        true: fn($query) => $query->whereNotNull('owner_response'),
                        false: fn($query) => $query->whereNull('owner_response'),
                    ),

                TernaryFilter::make('is_late_review')
                    ->label(__('review.filters_extra.late_review'))
                    ->boolean(),

                TernaryFilter::make('marketing_consent')
                    ->label(__('review.filters_extra.marketing_consent'))
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('approve')
                    ->label(__('review.actions.approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn(Review $record) => $record->approve())
                    ->visible(fn(Review $record) => !$record->is_approved),

                Action::make('reject')
                    ->label(__('review.actions.reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->schema([
                        Textarea::make('reason')
                            ->label(__('review.fields.rejection_reason'))
                            ->required(),
                    ])
                    ->action(fn(Review $record, array $data) => $record->reject($data['reason']))
                    ->visible(fn(Review $record) => $record->is_approved),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label(__('review.actions.approve'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->approve()),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('review.sections.review_information'))
                    ->schema([
                        TextEntry::make('hall.name')
                            ->label(__('review.fields.hall')),
                        TextEntry::make('user.name')
                            ->label(__('review.fields.user')),
                        TextEntry::make('booking.booking_number')
                            ->label(__('review.fields.booking')),
                        TextEntry::make('rating')
                            ->label(__('review.fields.rating'))
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                5 => 'success',
                                4 => 'success',
                                3 => 'warning',
                                2 => 'danger',
                                1 => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn($state) => str_repeat('⭐', $state)),
                    ])->columns(2),

                Section::make(__('review.fields.comment'))
                    ->schema([
                        TextEntry::make('comment')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('review.sections.detailed_ratings'))
                    ->schema([
                        TextEntry::make('cleanliness_rating')
                            ->label(__('review.fields.cleanliness_rating')),
                        TextEntry::make('service_rating')
                            ->label(__('review.fields.service_rating')),
                        TextEntry::make('value_rating')
                            ->label(__('review.fields.value_rating')),
                        TextEntry::make('location_rating')
                            ->label(__('review.fields.location_rating')),
                    ])->columns(4),

                Section::make(__('review.sections.moderation'))
                    ->schema([
                        TextEntry::make('is_approved')
                            ->label(__('review.fields.is_approved'))
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'warning')
                            ->formatStateUsing(fn($state) => $state
                                ? __('review.status.approved')
                                : __('review.status.pending')),
                        TextEntry::make('is_featured')
                            ->label(__('review.fields.is_featured'))
                            ->badge()
                            ->color(fn($state) => $state ? 'warning' : 'gray')
                            ->formatStateUsing(fn($state) => $state
                                ? __('review.status.featured')
                                : __('review.status.not_featured')),
                        IconEntry::make('is_late_review')
                            ->label(__('review.columns_extra.is_late_review'))
                            ->boolean(),
                        IconEntry::make('marketing_consent')
                            ->label(__('review.columns_extra.marketing_consent'))
                            ->boolean(),
                        TextEntry::make('admin_notes')
                            ->label(__('review.fields.admin_notes')),
                    ])->columns(3),

                Section::make(__('review.sections.owner_response'))
                    ->schema([
                        TextEntry::make('owner_response')
                            ->label(__('review.fields.owner_response'))
                            ->placeholder(__('review.placeholder.no_response')),
                        TextEntry::make('owner_response_at')
                            ->label(__('review.fields.owner_response_at')),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'create' => CreateReview::route('/create'),
            'view' => ViewReview::route('/{record}'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_approved', false)->count();
    }
}
