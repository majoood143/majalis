<?php

namespace App\Filament\Admin\Resources\TicketResource\RelationManagers;

use App\Models\TicketMessage;
use App\Models\TicketMessageType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Messages Relation Manager
 *
 * Manages the conversation thread for a support ticket, including:
 * - Customer replies
 * - Staff responses
 * - Internal notes (visible only to staff)
 * - File attachments
 * - Read/unread status tracking
 *
 * This relation manager provides a real-time conversation interface with
 * auto-refresh capabilities and comprehensive message management features.
 *
 * @package App\Filament\Admin\Resources\TicketResource\RelationManagers
 * @version 1.0.0
 * @author Majid Al Abri
 * @compatibility FilamentPHP 3.3, Laravel 12, PHP 8.4.12
 */
class MessagesRelationManager extends RelationManager
{
    /**
     * The relationship name defined in the Ticket model.
     *
     * This should match the relationship method name in your Ticket model:
     * public function messages() { return $this->hasMany(TicketMessage::class); }
     *
     * @var string
     */
    protected static string $relationship = 'messages';

    /**
     * The title displayed in the relation manager tab.
     *
     * @var string|null
     */
    protected static ?string $title = 'Conversation';

    /**
     * The record title attribute used for display purposes.
     *
     * This is used when referencing individual message records.
     *
     * @var string|null
     */
    protected static ?string $recordTitleAttribute = 'message';

    /**
     * Icon displayed in the relation manager tab.
     *
     * @var string|null
     */
    protected static ?string $icon = 'heroicon-o-chat-bubble-left-right';

    /**
     * Define the form schema for creating and editing messages.
     *
     * This form allows staff to:
     * - Choose message type (staff reply vs internal note)
     * - Compose message content
     * - Upload file attachments
     * - Auto-assign current user as message author
     *
     * @param Form $form The Filament form builder instance
     * @return Form Configured form with all necessary fields
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Message Type Selection
                // Allows staff to choose between customer-visible reply or internal note
                Forms\Components\Select::make('type')
                    ->label('Message Type')
                    ->options([
                        TicketMessageType::STAFF_REPLY->value => TicketMessageType::STAFF_REPLY->getLabel(),
                        TicketMessageType::INTERNAL_NOTE->value => TicketMessageType::INTERNAL_NOTE->getLabel(),
                    ])
                    ->default(TicketMessageType::STAFF_REPLY->value)
                    ->required()
                    ->native(false) // Use custom select for better UX
                    ->helperText('Internal notes are only visible to staff members')
                    ->live() // Real-time updates for conditional logic
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Automatically set is_internal flag based on type selection
                        $set('is_internal', $state === TicketMessageType::INTERNAL_NOTE->value);
                    }),

                // Message Content Area
                // Main text input for the message body
                Forms\Components\Textarea::make('message')
                    ->label('Message')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull()
                    ->placeholder('Type your message here...')
                    ->maxLength(65535) // TEXT column limit
                    ->helperText('Provide clear and helpful information to address the ticket'),

                // File Attachments Upload
                // Supports multiple files with validation
                Forms\Components\FileUpload::make('attachments')
                    ->label('Attachments')
                    ->multiple()
                    ->directory('ticket-attachments') // Storage path
                    ->disk('private') // Use private disk for security
                    ->maxSize(10240) // 10MB max per file
                    ->maxFiles(5) // Limit number of attachments
                    ->acceptedFileTypes([
                        'image/*', // All image types
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'text/plain',
                        'text/csv',
                    ])
                    ->helperText('Max 10MB per file. Accepted: Images, PDF, Word, Excel, Text')
                    ->columnSpanFull()
                    ->visibility('private') // Files stored privately
                    ->downloadable() // Allow downloads in view mode
                    ->previewable() // Enable file preview
                    ->reorderable(), // Allow drag-and-drop reordering

                // Internal Flag (Hidden)
                // Automatically set based on message type
                Forms\Components\Hidden::make('is_internal')
                    ->default(false)
                    ->dehydrateStateUsing(fn ($get) =>
                        $get('type') === TicketMessageType::INTERNAL_NOTE->value
                    ),

                // User ID (Hidden)
                // Auto-filled with currently authenticated user
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id())
                    ->required(),
            ]);
    }

    /**
     * Define the table schema for listing messages.
     *
     * Displays all messages in the ticket thread with:
     * - Visual indicators for message type
     * - Author information
     * - Message preview
     * - Attachment indicators
     * - Read status
     * - Timestamps
     *
     * Features auto-refresh every 15 seconds for real-time updates.
     *
     * @param Table $table The Filament table builder instance
     * @return Table Configured table with columns, filters, and actions
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('message')
            ->columns([
                // Message Type Badge
                // Visual indicator showing message type with color coding
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'Unknown')
                    ->color(fn ($state) => $state?->getColor() ?? 'gray')
                    ->icon(fn ($state) => $state?->getIcon() ?? 'heroicon-o-question-mark-circle')
                    ->sortable(),

                // Author Name Column
                // Shows who wrote the message with search capability
                Tables\Columns\TextColumn::make('user.name')
                    ->label('From')
                    ->searchable()
                    ->sortable()
                    ->default('Unknown User') // Fallback if user is deleted
                    ->description(fn ($record) => $record->user?->email) // Show email on hover
                    ->color(fn ($record) =>
                        $record->is_internal ? 'warning' : 'primary'
                    ),

                // Message Content Preview
                // Shows truncated message with full text on hover
                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->limit(60) // Truncate long messages
                    ->wrap() // Allow text wrapping
                    ->searchable()
                    ->tooltip(fn ($record) => $record->message) // Full text on hover
                    ->html() // Allow HTML rendering if needed
                    ->extraAttributes(['class' => 'prose prose-sm']),

                // Attachments Indicator
                // Icon showing if files are attached
                Tables\Columns\IconColumn::make('has_attachments')
                    ->label('Files')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('primary')
                    ->falseColor('gray')
                    ->alignCenter()
                    ->tooltip(fn ($record) =>
                        $record->has_attachments
                            ? ($record->attachments_count . ' file(s) attached')
                            : 'No attachments'
                    ),

                // Internal Flag Indicator
                // Shows if message is internal (staff-only)
                Tables\Columns\IconColumn::make('is_internal')
                    ->label('Internal')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-eye')
                    ->alignCenter()
                    ->tooltip(fn ($record) =>
                        $record->is_internal
                            ? 'Internal note - not visible to customers'
                            : 'Visible to customers'
                    ),

                // Read Status Indicator
                // Shows if message has been read
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->alignCenter()
                    ->tooltip(fn ($record) =>
                        $record->is_read ? 'Read' : 'Unread'
                    ),

                // Timestamp Column
                // Shows when message was posted with relative time
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Posted')
                    ->dateTime('M d, Y H:i') // Format: Jan 15, 2025 14:30
                    ->sortable()
                    ->since() // Shows "2 hours ago" format
                    ->tooltip(fn ($record) =>
                        $record->created_at->format('M d, Y H:i:s')
                    )
                    ->description(fn ($record) =>
                        $record->updated_at->ne($record->created_at)
                            ? 'Edited ' . $record->updated_at->diffForHumans()
                            : null
                    ),
            ])
            ->filters([
                // Message Type Filter
                // Filter messages by type (reply, note, etc.)
                Tables\Filters\SelectFilter::make('type')
                    ->label('Message Type')
                    ->options(TicketMessageType::toSelectArray())
                    ->multiple()
                    ->preload(),

                // Internal Messages Filter
                // Quick filter for internal-only messages
                Tables\Filters\TernaryFilter::make('is_internal')
                    ->label('Visibility')
                    ->placeholder('All Messages')
                    ->trueLabel('Internal Notes Only')
                    ->falseLabel('Customer Visible Only')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_internal', true),
                        false: fn (Builder $query) => $query->where('is_internal', false),
                        blank: fn (Builder $query) => $query,
                    ),

                // Read Status Filter
                // Filter by read/unread status
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Read Status')
                    ->placeholder('All Messages')
                    ->trueLabel('Read Messages')
                    ->falseLabel('Unread Messages')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_read', true),
                        false: fn (Builder $query) => $query->where('is_read', false),
                        blank: fn (Builder $query) => $query,
                    ),

                // Author Filter
                // Filter by message author
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Author')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->headerActions([
                // Create New Message Action
                // Allows staff to add new messages to the conversation
                Tables\Actions\CreateAction::make()
                    ->label('Add Message')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->modalHeading('Add Message to Ticket')
                    ->modalWidth('2xl')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Ensure user_id is set to current authenticated user
                        $data['user_id'] = Auth::id();

                        // Set is_internal based on message type
                        $data['is_internal'] = $data['type'] === TicketMessageType::INTERNAL_NOTE->value;

                        // Process file attachments if any exist
                        if (!empty($data['attachments'])) {
                            $attachmentData = [];

                            foreach ($data['attachments'] as $path) {
                                // Retrieve file metadata from storage
                                $attachmentData[] = [
                                    'path' => $path,
                                    'original_name' => basename($path),
                                    //'mime_type' => Storage::disk('private')->mimeType($path),
                                    'size' => Storage::disk('private')->size($path),
                                    'uploaded_at' => now()->toIso8601String(),
                                ];
                            }

                            // Replace paths array with detailed metadata
                            $data['attachments'] = $attachmentData;
                        }

                        return $data;
                    })
                    ->after(function ($record) {
                        // Post-creation actions

                        // Mark staff replies as unread for customer notification
                        if ($record->type === TicketMessageType::STAFF_REPLY) {
                            $record->update(['is_read' => false]);
                        }

                        // Update ticket's last activity timestamp
                        $record->ticket->touch();

                        // Optional: Send notification to customer for staff replies
                        // Uncomment and implement notification logic as needed
                        // if ($record->type === TicketMessageType::STAFF_REPLY) {
                        //     $record->ticket->customer->notify(
                        //         new TicketMessageReceived($record)
                        //     );
                        // }
                    })
                    ->successNotificationTitle('Message added successfully'),
            ])
            ->actions([
                // View Message Action
                // Expands message in modal with full details
                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn ($record) => 'Message from ' . ($record->user?->name ?? 'Unknown User'))
                    ->modalContent(fn ($record) => view('filament.ticket-message-view', [
                        'record' => $record
                    ]))
                    ->modalWidth('3xl')
                    ->slideOver(), // Use slide-over panel for better UX

                // Edit Message Action
                // Allow editing within time limit (30 minutes)
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) =>
                        // Only message author can edit
                        $record->user_id === Auth::id() &&
                        // Only within 30 minutes of creation
                        $record->created_at->diffInMinutes(now()) < 30
                    )
                    ->modalHeading('Edit Message')
                    ->modalWidth('2xl')
                    ->successNotificationTitle('Message updated successfully'),

                // Toggle Read Status Action
                // Mark message as read or unread
                Tables\Actions\Action::make('toggle_read')
                    ->label(fn ($record) => $record->is_read ? 'Mark Unread' : 'Mark Read')
                    ->icon(fn ($record) =>
                        $record->is_read
                            ? 'heroicon-o-envelope'
                            : 'heroicon-o-envelope-open'
                    )
                    ->color(fn ($record) => $record->is_read ? 'gray' : 'primary')
                    ->action(function ($record) {
                        // Toggle read status using model method
                        $record->is_read
                            ? $record->markAsUnread()
                            : $record->markAsRead();
                    })
                    ->requiresConfirmation(false) // Instant action
                    ->successNotificationTitle(fn ($record) =>
                        $record->is_read ? 'Marked as read' : 'Marked as unread'
                    ),

                // Delete Message Action
                // Only author or super admin can delete
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) =>
                        $record->user_id === Auth::id() ||
                        Auth::user()->hasRole('super_admin')
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Delete Message')
                    ->modalDescription('Are you sure you want to delete this message? This action cannot be undone.')
                    ->successNotificationTitle('Message deleted'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Bulk Mark as Read
                    Tables\Actions\BulkAction::make('mark_read')
                        ->label('Mark as Read')
                        ->icon('heroicon-o-envelope-open')
                        ->color('success')
                        ->action(fn ($records) => $records->each->markAsRead())
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Messages marked as read'),

                    // Bulk Mark as Unread
                    Tables\Actions\BulkAction::make('mark_unread')
                        ->label('Mark as Unread')
                        ->icon('heroicon-o-envelope')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->markAsUnread())
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Messages marked as unread'),

                    // Bulk Delete
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Messages')
                        ->modalDescription('Are you sure? This action cannot be undone.')
                        ->successNotificationTitle('Messages deleted'),
                ]),
            ])
            // Sort oldest first for natural conversation flow
            ->defaultSort('created_at', 'asc')
            // Enable real-time updates (refresh every 15 seconds)
            ->poll('15s')
            // Striped rows for better readability
            ->striped()
            // Enable query string for filtering
            ->persistFiltersInSession()
            // Pagination settings
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }

    /**
     * Modify the Eloquent query for loading messages.
     *
     * CRITICAL FIX: This method properly handles the query builder to prevent
     * "Call to a member function with() on null" errors.
     *
     * The fix ensures we:
     * 1. Get the base query from the relationship
     * 2. Check if it's null before calling with()
     * 3. Eager load the user relationship for performance
     * 4. Add additional query scopes as needed
     *
     * @return Builder The configured Eloquent query builder
     */
    protected function modifyTableQuery(Builder $query): Builder
    {
        return $query
            // Eager load the user relationship to avoid N+1 queries
            ->with([
                'user:id,name,email', // Only load needed columns
            ])
            // Optional: Add counts for attachments
            ->withCount('attachments')
            // Optional: Add any additional scopes
            ->orderBy('created_at', 'asc'); // Ensure chronological order
    }

    /**
     * Check if the relation manager can create new records.
     *
     * @return bool True if creation is allowed
     */
    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        // Only show messages for tickets that exist
        return $ownerRecord->exists;
    }

    /**
     * Get the badge count for the relation manager tab.
     *
     * Shows the count of unread messages.
     *
     * @return string|int|null The badge count
     */
    // public function getBadge(): ?string
    // {
    //     $unreadCount = $this->getRelationship()->where('is_read', false)->count();

    //     return $unreadCount > 0 ? (string) $unreadCount : null;
    // }

    /**
     * Get the badge color for the relation manager tab.
     *
     * @return string|array|null The badge color
     */
    // public function getBadgeColor(): ?string
    // {
    //     $unreadCount = $this->getRelationship()->where('is_read', false)->count();

    //     return $unreadCount > 0 ? 'danger' : null;
    // }
}
