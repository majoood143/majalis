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

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'ticket_number';

    public static function getNavigationGroup(): ?string
    {
        return __('owner.nav_groups.support');
    }

    public static function getNavigationLabel(): string
    {
        return __('owner.tickets.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('owner.tickets.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('owner.tickets.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getHallOwnerTicketsQuery()->open()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $count = static::getHallOwnerTicketsQuery()->open()->count();

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
                Forms\Components\Section::make(__('owner.tickets.sections.ticket_information'))
                    ->schema([
                        Forms\Components\TextInput::make('ticket_number')
                            ->label(__('owner.tickets.fields.ticket_number'))
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null),

                        Forms\Components\Select::make('type')
                            ->label(__('owner.tickets.fields.type'))
                            ->options(TicketType::toSelectArray())
                            ->required()
                            ->native(false)
                            ->searchable(),

                        Forms\Components\Select::make('priority')
                            ->label(__('owner.tickets.fields.priority'))
                            ->options(TicketPriority::toSelectArray())
                            ->default(TicketPriority::MEDIUM->value)
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->label(__('owner.tickets.fields.status'))
                            ->options(TicketStatus::toSelectArray())
                            ->default(TicketStatus::OPEN->value)
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null),

                        Forms\Components\TextInput::make('subject')
                            ->label(__('owner.tickets.fields.subject'))
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label(__('owner.tickets.fields.description'))
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('owner.tickets.sections.related_booking'))
                    ->schema([
                        Forms\Components\Select::make('booking_id')
                            ->label(__('owner.tickets.fields.booking'))
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
                            ->helperText(__('owner.tickets.helpers.booking')),
                    ]),

                Forms\Components\Section::make(__('owner.tickets.sections.resolution'))
                    ->schema([
                        Forms\Components\Textarea::make('resolution')
                            ->label(__('owner.tickets.fields.resolution'))
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
                    ->label(__('owner.tickets.columns.ticket_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('subject')
                    ->label(__('owner.tickets.columns.subject'))
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->subject)
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label(__('owner.tickets.columns.type'))
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon())
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('priority')
                    ->label(__('owner.tickets.columns.priority'))
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('owner.tickets.columns.status'))
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking.id')
                    ->label(__('owner.tickets.columns.booking'))
                    ->prefix('#')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('owner.tickets.columns.submitted'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->since()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('owner.tickets.columns.last_update'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('owner.tickets.filters.status'))
                    ->options(TicketStatus::toSelectArray())
                    ->multiple(),

                Tables\Filters\SelectFilter::make('priority')
                    ->label(__('owner.tickets.filters.priority'))
                    ->options(TicketPriority::toSelectArray())
                    ->multiple(),

                Tables\Filters\SelectFilter::make('type')
                    ->label(__('owner.tickets.filters.type'))
                    ->options(TicketType::toSelectArray())
                    ->multiple(),

                Tables\Filters\Filter::make('open')
                    ->label(__('owner.tickets.filters.open_tickets'))
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

    /**
     * Allow any authenticated owner to access this resource.
     * Data is already scoped to the owner via getEloquentQuery.
     *
     * @return bool
     */
    public static function canAccess(): bool
    {
        return Auth::check();
    }

    public static function canViewAny(): bool
    {
        return Auth::check();
    }

    protected static function getHallOwnerTicketsQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('booking.hall', fn (Builder $q) => $q->where('owner_id', Auth::id()));
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
