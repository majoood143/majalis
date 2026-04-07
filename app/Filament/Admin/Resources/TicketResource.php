<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TicketResource\Pages;
use App\Filament\Admin\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\ActionGroup;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'ticket_number';

    public static function getNavigationGroup(): ?string
    {
        return __('ticket_admin.nav_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('ticket_admin.nav_label');
    }

    public static function getPluralLabel(): string
    {
        return __('ticket_admin.nav_plural');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::open()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::open()->count();

        if ($count > 10) return 'danger';
        if ($count > 5)  return 'warning';
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('ticket_admin.section_info'))
                    ->description(__('ticket_admin.section_info_desc'))
                    ->schema([
                        Forms\Components\TextInput::make('ticket_number')
                            ->label(__('ticket_admin.ticket_number'))
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null),

                        Forms\Components\Select::make('type')
                            ->label(__('ticket_admin.type'))
                            ->options(TicketType::toSelectArray())
                            ->required()
                            ->native(false)
                            ->searchable(),

                        Forms\Components\Select::make('priority')
                            ->label(__('ticket_admin.priority'))
                            ->options(TicketPriority::toSelectArray())
                            ->default(TicketPriority::MEDIUM->value)
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->label(__('ticket_admin.status'))
                            ->options(TicketStatus::toSelectArray())
                            ->default(TicketStatus::OPEN->value)
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('subject')
                            ->label(__('ticket_admin.subject'))
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull()
                            ->placeholder(__('ticket_admin.subject_placeholder')),

                        Forms\Components\Textarea::make('description')
                            ->label(__('ticket_admin.description'))
                            ->required()
                            ->rows(5)
                            ->columnSpanFull()
                            ->placeholder(__('ticket_admin.description_placeholder')),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('ticket_admin.section_assignment'))
                    ->description(__('ticket_admin.section_assignment_desc'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('ticket_admin.customer'))
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText(__('ticket_admin.customer_help')),

                        Forms\Components\Select::make('assigned_to')
                            ->label(__('ticket_admin.assigned_to'))
                            ->relationship('assignedTo', 'name', fn (Builder $query) =>
                                $query->whereHas('roles', fn ($q) =>
                                    $q->whereIn('name', ['admin', 'staff', 'super_admin'])
                                )
                            )
                            ->searchable()
                            ->preload()
                            ->helperText(__('ticket_admin.assigned_to_help')),

                        Forms\Components\Select::make('booking_id')
                            ->label(__('ticket_admin.related_booking'))
                            ->relationship('booking', 'id')
                            ->searchable()
                            ->preload()
                            ->helperText(__('ticket_admin.related_booking_help'))
                            ->getOptionLabelFromRecordUsing(fn ($record) =>
                                "#{$record->id} - {$record->hall?->name} ({$record->booking_date->format('M d, Y')})"
                            ),

                        Forms\Components\DateTimePicker::make('due_date')
                            ->label(__('ticket_admin.due_date'))
                            ->helperText(__('ticket_admin.due_date_help'))
                            ->native(false)
                            ->seconds(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('ticket_admin.section_resolution'))
                    ->description(__('ticket_admin.section_resolution_desc'))
                    ->schema([
                        Forms\Components\Textarea::make('resolution')
                            ->label(__('ticket_admin.resolution'))
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder(__('ticket_admin.resolution_placeholder'))
                            ->visible(fn ($record) =>
                                $record && in_array($record->status, [
                                    TicketStatus::RESOLVED,
                                    TicketStatus::CLOSED
                                ])
                            ),

                        Forms\Components\Textarea::make('internal_notes')
                            ->label(__('ticket_admin.internal_notes'))
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder(__('ticket_admin.internal_notes_placeholder'))
                            ->helperText(__('ticket_admin.internal_notes_help')),

                        Forms\Components\Select::make('rating')
                            ->label(__('ticket_admin.rating'))
                            ->options([
                                1 => __('ticket_admin.rating_1'),
                                2 => __('ticket_admin.rating_2'),
                                3 => __('ticket_admin.rating_3'),
                                4 => __('ticket_admin.rating_4'),
                                5 => __('ticket_admin.rating_5'),
                            ])
                            ->native(false)
                            ->visible(fn ($record) => $record && $record->status === TicketStatus::CLOSED),

                        Forms\Components\Textarea::make('feedback')
                            ->label(__('ticket_admin.feedback'))
                            ->rows(3)
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record && $record->status === TicketStatus::CLOSED),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => !$record || !in_array($record->status, [
                        TicketStatus::RESOLVED,
                        TicketStatus::CLOSED
                    ])),

                Forms\Components\Section::make(__('ticket_admin.section_timeline'))
                    ->description(__('ticket_admin.section_timeline_desc'))
                    ->schema([
                        Forms\Components\DateTimePicker::make('first_response_at')
                            ->label(__('ticket_admin.first_response_at'))
                            ->disabled()
                            ->visible(fn ($record) => $record?->first_response_at),

                        Forms\Components\DateTimePicker::make('resolved_at')
                            ->label(__('ticket_admin.resolved_at'))
                            ->disabled()
                            ->visible(fn ($record) => $record?->resolved_at),

                        Forms\Components\DateTimePicker::make('closed_at')
                            ->label(__('ticket_admin.closed_at'))
                            ->disabled()
                            ->visible(fn ($record) => $record?->closed_at),
                    ])
                    ->columns(3)
                    ->collapsed()
                    ->visible(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label(__('ticket_admin.col_ticket_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('subject')
                    ->label(__('ticket_admin.col_subject'))
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->subject)
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label(__('ticket_admin.col_type'))
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon())
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('priority')
                    ->label(__('ticket_admin.col_priority'))
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon())
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('ticket_admin.col_status'))
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon())
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('ticket_admin.col_customer'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label(__('ticket_admin.col_assigned_to'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('ticket_admin.unassigned'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('booking.id')
                    ->label(__('ticket_admin.col_booking'))
                    ->prefix('#')
                    ->url(fn ($record) => $record->booking ?
                        route('filament.admin.resources.bookings.view', $record->booking) : null
                    )
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('ticket_admin.col_due_date'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : null)
                    ->weight(fn ($record) => $record->is_overdue ? 'bold' : null)
                    ->icon(fn ($record) => $record->is_overdue ? 'heroicon-o-exclamation-circle' : null)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('ticket_admin.col_created'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('ticket_admin.col_updated'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('ticket_admin.filter_status'))
                    ->options(TicketStatus::toSelectArray())
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('priority')
                    ->label(__('ticket_admin.filter_priority'))
                    ->options(TicketPriority::toSelectArray())
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->label(__('ticket_admin.filter_type'))
                    ->options(TicketType::toSelectArray())
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label(__('ticket_admin.filter_assigned_to'))
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('my_tickets')
                    ->label(__('ticket_admin.filter_my_tickets'))
                    ->query(fn (Builder $query) => $query->where('assigned_to', Auth::id()))
                    ->toggle(),

                Tables\Filters\Filter::make('overdue')
                    ->label(__('ticket_admin.filter_overdue'))
                    ->query(fn (Builder $query) => $query->overdue())
                    ->toggle(),

                Tables\Filters\Filter::make('open')
                    ->label(__('ticket_admin.filter_open'))
                    ->query(fn (Builder $query) => $query->open())
                    ->toggle()
                    ->default(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('assign_to_me')
                        ->label(__('ticket_admin.action_assign_to_me'))
                        ->icon('heroicon-o-user-plus')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Ticket $record) {
                            $record->assignTo(Auth::id());

                            Notification::make()
                                ->title(__('ticket_admin.notif_assigned'))
                                ->body(__('ticket_admin.notif_assigned_body'))
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Ticket $record) => $record->assigned_to !== Auth::id()),

                    Tables\Actions\Action::make('resolve')
                        ->label(__('ticket_admin.action_resolve'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('resolution')
                                ->label(__('ticket_admin.resolution'))
                                ->required()
                                ->rows(4),
                        ])
                        ->action(function (Ticket $record, array $data) {
                            $record->resolve($data['resolution']);

                            Notification::make()
                                ->title(__('ticket_admin.notif_resolved'))
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Ticket $record) => in_array($record->status, [
                            TicketStatus::OPEN,
                            TicketStatus::IN_PROGRESS,
                            TicketStatus::PENDING
                        ])),

                    Tables\Actions\Action::make('close')
                        ->label(__('ticket_admin.action_close'))
                        ->icon('heroicon-o-lock-closed')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(function (Ticket $record) {
                            $record->close();

                            Notification::make()
                                ->title(__('ticket_admin.notif_closed'))
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Ticket $record) => $record->status === TicketStatus::RESOLVED),

                    Tables\Actions\DeleteAction::make(),
                    ActivityLogTimelineTableAction::make('Activities'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('assign')
                        ->label(__('ticket_admin.action_bulk_assign'))
                        ->icon('heroicon-o-user-plus')
                        ->form([
                            Forms\Components\Select::make('assigned_to')
                                ->label(__('ticket_admin.bulk_assign_field'))
                                ->options(User::whereHas('roles', fn ($q) =>
                                    $q->whereIn('name', ['admin', 'staff', 'super_admin'])
                                )->pluck('name', 'id'))
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $record->assignTo($data['assigned_to']);
                            }

                            Notification::make()
                                ->title(__('ticket_admin.notif_tickets_assigned'))
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MessagesRelationManager::class,
        ];
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
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
