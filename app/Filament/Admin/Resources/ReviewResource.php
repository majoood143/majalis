<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Booking Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Review Information')
                    ->schema([
                        Forms\Components\Select::make('hall_id')
                            ->relationship('hall', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(),

                        Forms\Components\Select::make('booking_id')
                            ->relationship('booking', 'booking_number')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(),

                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(),

                        Forms\Components\Select::make('rating')
                            ->options([
                                1 => '⭐ 1 Star',
                                2 => '⭐⭐ 2 Stars',
                                3 => '⭐⭐⭐ 3 Stars',
                                4 => '⭐⭐⭐⭐ 4 Stars',
                                5 => '⭐⭐⭐⭐⭐ 5 Stars',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('comment')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Detailed Ratings')
                    ->schema([
                        Forms\Components\Select::make('cleanliness_rating')
                            ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),

                        Forms\Components\Select::make('service_rating')
                            ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),

                        Forms\Components\Select::make('value_rating')
                            ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),

                        Forms\Components\Select::make('location_rating')
                            ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),
                    ])->columns(4)
                    ->collapsible(),

                Forms\Components\Section::make('Moderation')
                    ->schema([
                        Forms\Components\Toggle::make('is_approved')
                            ->inline(false),

                        Forms\Components\Toggle::make('is_featured')
                            ->inline(false),

                        Forms\Components\Textarea::make('admin_notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Owner Response')
                    ->schema([
                        Forms\Components\Textarea::make('owner_response')
                            ->rows(4)
                            ->columnSpanFull()
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('owner_response_at')
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
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->hall->name),

                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->badge()
                    ->sortable()
                    ->color(fn(int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn(int $state): string => str_repeat('⭐', $state)),

                Tables\Columns\TextColumn::make('comment')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_approved')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('owner_response')
                    ->limit(30)
                    ->toggleable()
                    ->placeholder('No response'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hall_id')
                    ->relationship('hall', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        1 => '1 Star',
                        2 => '2 Stars',
                        3 => '3 Stars',
                        4 => '4 Stars',
                        5 => '5 Stars',
                    ]),

                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Approved')
                    ->boolean()
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->native(false),

                Tables\Filters\TernaryFilter::make('owner_response')
                    ->label('Has Owner Response')
                    ->queries(
                        true: fn($query) => $query->whereNotNull('owner_response'),
                        false: fn($query) => $query->whereNull('owner_response'),
                    )
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn(Review $record) => $record->approve())
                    ->visible(fn(Review $record) => !$record->is_approved),

                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->required(),
                    ])
                    ->action(fn(Review $record, array $data) => $record->reject($data['reason']))
                    ->visible(fn(Review $record) => $record->is_approved),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->approve()),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_approved', false)->count();
    }
}
