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


/**
 * Ticket Resource for Filament Admin Panel
 *
 * Manages customer support tickets and claims with comprehensive features:
 * - Full CRUD operations
 * - Status workflow management
 * - Assignment to staff members
 * - Priority and SLA tracking
 * - Message/response management
 * - File attachment support
 * - Customer satisfaction ratings
 *
 * @package App\Filament\Admin\Resources
 * @version 1.0.0
 */
class TicketResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    protected static ?string $model = Ticket::class;

    /**
     * The navigation icon for the resource.
     *
     * @var string
     */
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    /**
     * The navigation group the resource belongs to.
     *
     * @var string
     */
    protected static ?string $navigationGroup = 'Support';

    /**
     * The navigation sort order.
     *
     * @var int
     */
    protected static ?int $navigationSort = 1;

    /**
     * The default sort column for the table.
     *
     * @var string
     */
    protected static ?string $recordTitleAttribute = 'ticket_number';

    /**
     * Get the navigation badge for the resource (shows count of open tickets).
     *
     * @return string|null
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::open()->count() ?: null;
    }

    /**
     * Get the navigation badge color.
     *
     * @return string|null
     */
    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::open()->count();

        if ($count > 10) {
            return 'danger';
        }

        if ($count > 5) {
            return 'warning';
        }

        return 'success';
    }

    /**
     * Define the form schema for creating/editing tickets.
     *
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main Ticket Information Section
                Forms\Components\Section::make('Ticket Information')
                    ->description('Basic ticket details and classification')
                    ->schema([
                        // Ticket number (read-only, auto-generated)
                        Forms\Components\TextInput::make('ticket_number')
                            ->label('Ticket Number')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null),

                        // Ticket type selection
                        Forms\Components\Select::make('type')
                            ->label('Ticket Type')
                            ->options(TicketType::toSelectArray())
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->helperText('Select the type of issue or request'),

                        // Priority selection with color indicators
                        Forms\Components\Select::make('priority')
                            ->label('Priority')
                            ->options(TicketPriority::toSelectArray())
                            ->default(TicketPriority::MEDIUM->value)
                            ->required()
                            ->native(false)
                            ->helperText('Higher priority tickets are resolved faster'),

                        // Status selection (with workflow validation)
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(TicketStatus::toSelectArray())
                            ->default(TicketStatus::OPEN->value)
                            ->required()
                            ->native(false)
                            ->helperText('Current status of the ticket'),

                        // Subject/Title
                        Forms\Components\TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull()
                            ->placeholder('Brief description of the issue'),

                        // Detailed description
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull()
                            ->placeholder('Provide detailed information about the issue or request'),
                    ])
                    ->columns(2),

                // Assignment and Relationships Section
                Forms\Components\Section::make('Assignment & Relations')
                    ->description('Link ticket to booking and assign to staff')
                    ->schema([
                        // Customer/User selection
                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('The customer who submitted this ticket'),

                        // Assigned staff member
                        Forms\Components\Select::make('assigned_to')
                            ->label('Assigned To')
                            ->relationship('assignedTo', 'name', fn (Builder $query) =>
                                $query->whereHas('roles', fn ($q) =>
                                    $q->whereIn('name', ['admin', 'staff', 'super_admin'])
                                )
                            )
                            ->searchable()
                            ->preload()
                            ->helperText('Staff member responsible for handling this ticket'),

                        // Related booking (optional)
                        Forms\Components\Select::make('booking_id')
                            ->label('Related Booking')
                            ->relationship('booking', 'id')
                            ->searchable()
                            ->preload()
                            ->helperText('Link to a specific booking if applicable')
                            ->getOptionLabelFromRecordUsing(fn ($record) =>
                                "#{$record->id} - {$record->hall?->name} ({$record->booking_date->format('M d, Y')})"
                            ),

                        // Due date for resolution
                        Forms\Components\DateTimePicker::make('due_date')
                            ->label('Due Date')
                            ->helperText('Target resolution date (auto-calculated based on priority)')
                            ->native(false)
                            ->seconds(false),
                    ])
                    ->columns(2),

                // Resolution Section (visible when resolving/closed)
                Forms\Components\Section::make('Resolution')
                    ->description('Resolution details and customer feedback')
                    ->schema([
                        // Resolution description
                        Forms\Components\Textarea::make('resolution')
                            ->label('Resolution')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Describe how the issue was resolved')
                            ->visible(fn ($record) =>
                                $record && in_array($record->status, [
                                    TicketStatus::RESOLVED,
                                    TicketStatus::CLOSED
                                ])
                            ),

                        // Internal notes (staff only)
                        Forms\Components\Textarea::make('internal_notes')
                            ->label('Internal Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Internal notes (not visible to customer)')
                            ->helperText('These notes are only visible to staff members'),

                        // Customer rating
                        Forms\Components\Select::make('rating')
                            ->label('Customer Rating')
                            ->options([
                                1 => '⭐ 1 - Very Unsatisfied',
                                2 => '⭐⭐ 2 - Unsatisfied',
                                3 => '⭐⭐⭐ 3 - Neutral',
                                4 => '⭐⭐⭐⭐ 4 - Satisfied',
                                5 => '⭐⭐⭐⭐⭐ 5 - Very Satisfied',
                            ])
                            ->native(false)
                            ->visible(fn ($record) => $record && $record->status === TicketStatus::CLOSED),

                        // Customer feedback
                        Forms\Components\Textarea::make('feedback')
                            ->label('Customer Feedback')
                            ->rows(3)
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record && $record->status === TicketStatus::CLOSED),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => !$record || !in_array($record->status, [
                        TicketStatus::RESOLVED,
                        TicketStatus::CLOSED
                    ])),

                // Timestamps Section
                Forms\Components\Section::make('Timeline')
                    ->description('Ticket lifecycle timestamps')
                    ->schema([
                        Forms\Components\DateTimePicker::make('first_response_at')
                            ->label('First Response At')
                            ->disabled()
                            ->visible(fn ($record) => $record?->first_response_at),

                        Forms\Components\DateTimePicker::make('resolved_at')
                            ->label('Resolved At')
                            ->disabled()
                            ->visible(fn ($record) => $record?->resolved_at),

                        Forms\Components\DateTimePicker::make('closed_at')
                            ->label('Closed At')
                            ->disabled()
                            ->visible(fn ($record) => $record?->closed_at),
                    ])
                    ->columns(3)
                    ->collapsed()
                    ->visible(fn ($record) => $record !== null),
            ]);
    }

    /**
     * Define the table schema for listing tickets.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Ticket number with link
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Ticket #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                // Subject/Title
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->subject)
                    ->wrap(),

                // Ticket type with badge
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon())
                    ->sortable(),

                // Priority with badge
                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Priority')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon())
                    ->sortable(),

                // Status with badge
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon())
                    ->sortable(),

                // Customer name
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                // Assigned staff
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Unassigned')
                    ->toggleable(),

                // Related booking
                Tables\Columns\TextColumn::make('booking.id')
                    ->label('Booking')
                    ->prefix('#')
                    ->url(fn ($record) => $record->booking ?
                        route('filament.admin.resources.bookings.view', $record->booking) : null
                    )
                    ->placeholder('—')
                    ->toggleable(),

                // Due date with overdue indicator
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : null)
                    ->weight(fn ($record) => $record->is_overdue ? 'bold' : null)
                    ->icon(fn ($record) => $record->is_overdue ? 'heroicon-o-exclamation-circle' : null)
                    ->toggleable(),

                // Created at
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                // Updated at
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(),
            ])
            ->filters([
                // Status filter
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(TicketStatus::toSelectArray())
                    ->multiple()
                    ->preload(),

                // Priority filter
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Priority')
                    ->options(TicketPriority::toSelectArray())
                    ->multiple()
                    ->preload(),

                // Type filter
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options(TicketType::toSelectArray())
                    ->multiple()
                    ->preload(),

                // Assigned filter
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload(),

                // My tickets filter
                Tables\Filters\Filter::make('my_tickets')
                    ->label('My Tickets')
                    ->query(fn (Builder $query) => $query->where('assigned_to', Auth::id()))
                    ->toggle(),

                // Overdue filter
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue')
                    ->query(fn (Builder $query) => $query->overdue())
                    ->toggle(),

                // Open tickets filter
                Tables\Filters\Filter::make('open')
                    ->label('Open Tickets')
                    ->query(fn (Builder $query) => $query->open())
                    ->toggle()
                    ->default(),

                // Soft delete filter
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([
                // View action
                Tables\Actions\ViewAction::make(),

                // Edit action
                Tables\Actions\EditAction::make(),

                // Assign to me action
                Tables\Actions\Action::make('assign_to_me')
                    ->label('Assign to Me')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Ticket $record) {
                        $record->assignTo(Auth::id());

                        Notification::make()
                            ->title('Ticket Assigned')
                            ->body('Ticket has been assigned to you.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Ticket $record) => $record->assigned_to !== Auth::id()),

                // Resolve action
                Tables\Actions\Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('resolution')
                            ->label('Resolution')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function (Ticket $record, array $data) {
                        $record->resolve($data['resolution']);

                        Notification::make()
                            ->title('Ticket Resolved')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Ticket $record) => in_array($record->status, [
                        TicketStatus::OPEN,
                        TicketStatus::IN_PROGRESS,
                        TicketStatus::PENDING
                    ])),

                // Close action
                Tables\Actions\Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-o-lock-closed')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (Ticket $record) {
                        $record->close();

                        Notification::make()
                            ->title('Ticket Closed')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Ticket $record) => $record->status === TicketStatus::RESOLVED),

                // Delete action
                Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Bulk assign action
                    Tables\Actions\BulkAction::make('assign')
                        ->label('Assign To')
                        ->icon('heroicon-o-user-plus')
                        ->form([
                            Forms\Components\Select::make('assigned_to')
                                ->label('Assign To')
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
                                ->title('Tickets Assigned')
                                ->success()
                                ->send();
                        }),

                    // Bulk delete
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto-refresh every 30 seconds
    }

    /**
     * Get the relations available on the entity.
     *
     * @return array
     */
    public static function getRelations(): array
    {
        return [
            RelationManagers\MessagesRelationManager::class,
        ];
    }

    /**
     * Get the pages available for the resource.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view' => Pages\ViewTicket::route('/{record}'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    /**
     * Get the Eloquent query for resource.
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
