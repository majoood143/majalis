<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * ReviewsRelationManager for Owner Panel
 *
 * Displays reviews for a specific hall owned by the current user.
 * Owners can view reviews and add responses.
 */
class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('owner.relation.reviews');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                // Customer Info
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('owner.reviews.customer'))
                    ->searchable()
                    ->sortable(),

                // Overall Rating with Stars
                Tables\Columns\TextColumn::make('rating')
                    ->label(__('owner.reviews.rating'))
                    ->formatStateUsing(function (int $state): string {
                        $stars = str_repeat('★', $state) . str_repeat('☆', 5 - $state);
                        return $stars . " ({$state}/5)";
                    })
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),

                // Comment Preview
                Tables\Columns\TextColumn::make('comment')
                    ->label(__('owner.reviews.comment'))
                    ->limit(50)
                    ->tooltip(fn ($record): ?string => $record->comment)
                    ->searchable()
                    ->wrap(),

                // Owner Response Status
                Tables\Columns\IconColumn::make('has_response')
                    ->label(__('owner.reviews.responded'))
                    ->state(fn ($record): bool => !empty($record->owner_response))
                    ->boolean()
                    ->trueIcon('heroicon-o-chat-bubble-left-right')
                    ->falseIcon('heroicon-o-chat-bubble-left')
                    ->trueColor('success')
                    ->falseColor('gray'),

                // Approval Status (info only for owner)
                Tables\Columns\IconColumn::make('is_approved')
                    ->label(__('owner.reviews.approved'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                // Review Date
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('owner.reviews.date'))
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                // Rating Filter
                Tables\Filters\SelectFilter::make('rating')
                    ->label(__('owner.reviews.rating'))
                    ->options([
                        5 => '★★★★★ (5)',
                        4 => '★★★★☆ (4)',
                        3 => '★★★☆☆ (3)',
                        2 => '★★☆☆☆ (2)',
                        1 => '★☆☆☆☆ (1)',
                    ]),

                // Has Response Filter
                Tables\Filters\Filter::make('needs_response')
                    ->label(__('owner.reviews.needs_response'))
                    ->query(fn (Builder $query): Builder => $query->whereNull('owner_response')),

                // Approved Filter
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label(__('owner.reviews.approved')),
            ])
            ->actions([
                // View Details
                Tables\Actions\ViewAction::make()
                    ->modalHeading(__('owner.reviews.view_title'))
                    ->infolist([
                        Infolists\Components\Section::make(__('owner.reviews.rating_section'))
                            ->schema([
                                Infolists\Components\TextEntry::make('rating')
                                    ->label(__('owner.reviews.overall'))
                                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state) . str_repeat('☆', 5 - $state)),

                                Infolists\Components\Grid::make(4)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('cleanliness_rating')
                                            ->label(__('owner.reviews.cleanliness'))
                                            ->placeholder('-'),
                                        Infolists\Components\TextEntry::make('service_rating')
                                            ->label(__('owner.reviews.service'))
                                            ->placeholder('-'),
                                        Infolists\Components\TextEntry::make('value_rating')
                                            ->label(__('owner.reviews.value'))
                                            ->placeholder('-'),
                                        Infolists\Components\TextEntry::make('location_rating')
                                            ->label(__('owner.reviews.location'))
                                            ->placeholder('-'),
                                    ]),
                            ]),

                        Infolists\Components\Section::make(__('owner.reviews.content_section'))
                            ->schema([
                                Infolists\Components\TextEntry::make('comment')
                                    ->label(__('owner.reviews.comment'))
                                    ->markdown()
                                    ->columnSpanFull(),
                            ]),

                        Infolists\Components\Section::make(__('owner.reviews.response_section'))
                            ->schema([
                                Infolists\Components\TextEntry::make('owner_response')
                                    ->label('')
                                    ->markdown()
                                    ->placeholder(__('owner.reviews.no_response')),
                            ]),
                    ]),

                // Add/Edit Response
                Tables\Actions\Action::make('respond')
                    ->label(fn ($record): string => $record->owner_response 
                        ? __('owner.reviews.edit_response') 
                        : __('owner.reviews.add_response'))
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->form([
                        Forms\Components\Textarea::make('owner_response')
                            ->label(__('owner.reviews.your_response'))
                            ->required()
                            ->rows(5)
                            ->maxLength(2000)
                            ->default(fn ($record): ?string => $record->owner_response)
                            ->helperText(__('owner.reviews.response_help')),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->update([
                            'owner_response' => $data['owner_response'],
                            'owner_response_at' => now(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title(__('owner.reviews.response_saved'))
                            ->send();
                    }),

                // Delete Response (if exists)
                Tables\Actions\Action::make('delete_response')
                    ->label(__('owner.reviews.delete_response'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => !empty($record->owner_response))
                    ->action(function ($record): void {
                        $record->update([
                            'owner_response' => null,
                            'owner_response_at' => null,
                        ]);

                        Notification::make()
                            ->success()
                            ->title(__('owner.reviews.response_deleted'))
                            ->send();
                    }),
            ])
            ->emptyStateHeading(__('owner.reviews.empty_heading'))
            ->emptyStateDescription(__('owner.reviews.empty_description'))
            ->emptyStateIcon('heroicon-o-star');
    }
}
