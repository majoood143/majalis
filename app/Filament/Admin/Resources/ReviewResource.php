<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Booking Management';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('review.sections.review_information'))
                    ->schema([
                        Forms\Components\Select::make('hall_id')
                            ->relationship('hall', 'name')
                            ->label(__('review.fields.hall'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(),

                        Forms\Components\Select::make('booking_id')
                            ->relationship('booking', 'booking_number')
                            ->label(__('review.fields.booking'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(),

                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label(__('review.fields.user'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(),

                        Forms\Components\Select::make('rating')
                            ->label(__('review.fields.rating'))
                            ->options([
                                1 => __('review.ratings.1_star'),
                                2 => __('review.ratings.2_stars'),
                                3 => __('review.ratings.3_stars'),
                                4 => __('review.ratings.4_stars'),
                                5 => __('review.ratings.5_stars'),
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('comment')
                            ->label(__('review.fields.comment'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('review.sections.detailed_ratings'))
                    ->schema([
                        Forms\Components\Select::make('cleanliness_rating')
                            ->label(__('review.fields.cleanliness_rating'))
                            ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),

                        Forms\Components\Select::make('service_rating')
                            ->label(__('review.fields.service_rating'))
                            ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),

                        Forms\Components\Select::make('value_rating')
                            ->label(__('review.fields.value_rating'))
                            ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),

                        Forms\Components\Select::make('location_rating')
                            ->label(__('review.fields.location_rating'))
                            ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),
                    ])->columns(4)
                    ->collapsible(),

                Forms\Components\Section::make(__('review.sections.moderation'))
                    ->schema([
                        Forms\Components\Toggle::make('is_approved')
                            ->label(__('review.fields.is_approved'))
                            ->inline(false),

                        Forms\Components\Toggle::make('is_featured')
                            ->label(__('review.fields.is_featured'))
                            ->inline(false),

                        Forms\Components\Toggle::make('is_late_review')
                            ->label(__('review.columns_extra.is_late_review'))
                            ->inline(false)
                            ->disabled(),

                        Forms\Components\Toggle::make('marketing_consent')
                            ->label(__('review.columns_extra.marketing_consent'))
                            ->inline(false)
                            ->disabled(),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label(__('review.fields.admin_notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('review.sections.owner_response'))
                    ->schema([
                        Forms\Components\Textarea::make('owner_response')
                            ->label(__('review.fields.owner_response'))
                            ->rows(4)
                            ->columnSpanFull()
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('owner_response_at')
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
                Tables\Columns\TextColumn::make('hall.name')
                    ->label(__('review.columns.hall'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->hall->name),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('review.columns.user'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label(__('review.columns.rating'))
                    ->badge()
                    ->sortable()
                    ->color(fn(int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn(int $state): string => str_repeat('⭐', $state)),

                Tables\Columns\TextColumn::make('comment')
                    ->label(__('review.columns.comment'))
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_approved')
                    ->label(__('review.columns.is_approved'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label(__('review.columns.is_featured'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('owner_response')
                    ->label(__('review.columns.owner_response'))
                    ->limit(30)
                    ->toggleable()
                    ->placeholder(__('review.placeholder.no_response')),

                Tables\Columns\IconColumn::make('is_late_review')
                    ->label(__('review.columns_extra.is_late_review'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('marketing_consent')
                    ->label(__('review.columns_extra.marketing_consent'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('review.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hall_id')
                    ->relationship('hall', 'name')
                    ->label(__('review.filters.hall'))
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('rating')
                    ->label(__('review.filters.rating'))
                    ->options([
                        1 => __('review.ratings.1_star'),
                        2 => __('review.ratings.2_stars'),
                        3 => __('review.ratings.3_stars'),
                        4 => __('review.ratings.4_stars'),
                        5 => __('review.ratings.5_stars'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label(__('review.filters.approved'))
                    ->boolean()
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label(__('review.filters.featured'))
                    ->boolean()
                    ->native(false),

                Tables\Filters\TernaryFilter::make('owner_response')
                    ->label(__('review.filters.has_owner_response'))
                    ->queries(
                        true: fn($query) => $query->whereNotNull('owner_response'),
                        false: fn($query) => $query->whereNull('owner_response'),
                    )
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_late_review')
                    ->label(__('review.filters_extra.late_review'))
                    ->boolean()
                    ->native(false),

                Tables\Filters\TernaryFilter::make('marketing_consent')
                    ->label(__('review.filters_extra.marketing_consent'))
                    ->boolean()
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('approve')
                    ->label(__('review.actions.approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn(Review $record) => $record->approve())
                    ->visible(fn(Review $record) => !$record->is_approved),

                Tables\Actions\Action::make('reject')
                    ->label(__('review.actions.reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label(__('review.fields.rejection_reason'))
                            ->required(),
                    ])
                    ->action(fn(Review $record, array $data) => $record->reject($data['reason']))
                    ->visible(fn(Review $record) => $record->is_approved),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label(__('review.actions.approve'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->approve()),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('review.sections.review_information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('hall.name')
                            ->label(__('review.fields.hall')),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label(__('review.fields.user')),
                        Infolists\Components\TextEntry::make('booking.booking_number')
                            ->label(__('review.fields.booking')),
                        Infolists\Components\TextEntry::make('rating')
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

                Infolists\Components\Section::make(__('review.fields.comment'))
                    ->schema([
                        Infolists\Components\TextEntry::make('comment')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make(__('review.sections.detailed_ratings'))
                    ->schema([
                        Infolists\Components\TextEntry::make('cleanliness_rating')
                            ->label(__('review.fields.cleanliness_rating')),
                        Infolists\Components\TextEntry::make('service_rating')
                            ->label(__('review.fields.service_rating')),
                        Infolists\Components\TextEntry::make('value_rating')
                            ->label(__('review.fields.value_rating')),
                        Infolists\Components\TextEntry::make('location_rating')
                            ->label(__('review.fields.location_rating')),
                    ])->columns(4),

                Infolists\Components\Section::make(__('review.sections.moderation'))
                    ->schema([
                        Infolists\Components\TextEntry::make('is_approved')
                            ->label(__('review.fields.is_approved'))
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'warning')
                            ->formatStateUsing(fn($state) => $state
                                ? __('review.status.approved')
                                : __('review.status.pending')),
                        Infolists\Components\TextEntry::make('is_featured')
                            ->label(__('review.fields.is_featured'))
                            ->badge()
                            ->color(fn($state) => $state ? 'warning' : 'gray')
                            ->formatStateUsing(fn($state) => $state
                                ? __('review.status.featured')
                                : __('review.status.not_featured')),
                        Infolists\Components\IconEntry::make('is_late_review')
                            ->label(__('review.columns_extra.is_late_review'))
                            ->boolean(),
                        Infolists\Components\IconEntry::make('marketing_consent')
                            ->label(__('review.columns_extra.marketing_consent'))
                            ->boolean(),
                        Infolists\Components\TextEntry::make('admin_notes')
                            ->label(__('review.fields.admin_notes')),
                    ])->columns(3),

                Infolists\Components\Section::make(__('review.sections.owner_response'))
                    ->schema([
                        Infolists\Components\TextEntry::make('owner_response')
                            ->label(__('review.fields.owner_response'))
                            ->placeholder(__('review.placeholder.no_response')),
                        Infolists\Components\TextEntry::make('owner_response_at')
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'view' => Pages\ViewReview::route('/{record}'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_approved', false)->count();
    }
}
