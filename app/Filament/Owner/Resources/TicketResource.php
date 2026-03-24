<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Support';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'ticket_number';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->open()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $count = static::getEloquentQuery()->open()->count();

        return match (true) {
            $count > 10 => 'danger',
            $count > 5  => 'warning',
            default     => 'success',
        };
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket Information')
                    ->schema([
                        Forms\Components\TextInput::make('ticket_number')
                            ->label('Ticket Number')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null),

                        Forms\Components\Select::make('type')
                            ->label('Ticket Type')
                            ->options(TicketType::toSelectArray())
                            ->required()
                            ->native(false)
                            ->searchable(),

                        Forms\Components\Select::make('priority')
                            ->label('Priority')
                            ->options(TicketPriority::toSelectArray())
                            ->default(TicketPriority::MEDIUM->value)
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(TicketStatus::toSelectArray())
                            ->default(TicketStatus::OPEN->value)
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null),

                        Forms\Components\TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Related Booking')
                    ->schema([
                        Forms\Components\Select::make('booking_id')
                            ->label('Related Booking')
                            ->options(function () {
                                $user = Auth::user();
                                return \App\Models\Booking::whereHas('hall', fn ($q) => $q->where('owner_id', $user->id))
                                    ->with('hall')
                                    ->get()
                                    ->mapWithKeys(fn ($booking) =>
                                        [$booking->id => "#{$booking->id} - {$booking->hall?->name} ({$booking->booking_date->format('M d, Y')})"]
                                    );
                            })
                            ->searchable()
                            ->helperText('Link this ticket to a specific booking if applicable'),
                    ]),

                Forms\Components\Section::make('Resolution')
                    ->schema([
                        Forms\Components\Textarea::make('resolution')
                            ->label('Resolution')
                            ->rows(4)
                            ->columnSpanFull()
                            ->disabled()
                            ->visible(fn ($record) => $record?->resolution !== null),
                    ])
                    ->collapsible()
                    ->visible(fn ($record) => $record?->resolution !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Ticket #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->subject)
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon())
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Priority')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking.id')
                    ->label('Booking')
                    ->prefix('#')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->since()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Update')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(TicketStatus::toSelectArray())
                    ->multiple(),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Priority')
                    ->options(TicketPriority::toSelectArray())
                    ->multiple(),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options(TicketType::toSelectArray())
                    ->multiple(),

                Tables\Filters\Filter::make('open')
                    ->label('Open Tickets')
                    ->query(fn (Builder $query) => $query->open())
                    ->toggle()
                    ->default(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Ticket $record) => in_array($record->status, [
                        TicketStatus::OPEN,
                        TicketStatus::PENDING,
                    ])),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view'   => Pages\ViewTicket::route('/{record}'),
            'edit'   => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        return parent::getEloquentQuery()
            ->where(function (Builder $q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('booking.hall', fn (Builder $sq) => $sq->where('owner_id', $user->id));
            });
    }
}
