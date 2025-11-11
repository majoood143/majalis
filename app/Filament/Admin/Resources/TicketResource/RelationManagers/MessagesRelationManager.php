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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Messages Relation Manager
 * 
 * Manages the conversation thread for a ticket, including customer replies,
 * staff responses, internal notes, and file attachments.
 * 
 * @package App\Filament\Admin\Resources\TicketResource\RelationManagers
 * @version 1.0.0
 */
class MessagesRelationManager extends RelationManager
{
    /**
     * The relationship name.
     *
     * @var string
     */
    protected static string $relationship = 'messages';

    /**
     * The title for this relation manager.
     *
     * @var string
     */
    protected static ?string $title = 'Conversation';

    /**
     * The record title attribute.
     *
     * @var string
     */
    protected static ?string $recordTitleAttribute = 'message';

    /**
     * Define the form schema for creating/editing messages.
     *
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Message type selection
                Forms\Components\Select::make('type')
                    ->label('Message Type')
                    ->options([
                        TicketMessageType::STAFF_REPLY->value => TicketMessageType::STAFF_REPLY->getLabel(),
                        TicketMessageType::INTERNAL_NOTE->value => TicketMessageType::INTERNAL_NOTE->getLabel(),
                    ])
                    ->default(TicketMessageType::STAFF_REPLY->value)
                    ->required()
                    ->native(false)
                    ->helperText('Internal notes are only visible to staff'),

                // Message content
                Forms\Components\Textarea::make('message')
                    ->label('Message')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull()
                    ->placeholder('Type your message here...'),

                // File attachments
                Forms\Components\FileUpload::make('attachments')
                    ->label('Attachments')
                    ->multiple()
                    ->directory('ticket-attachments')
                    ->disk('private')
                    ->maxSize(10240) // 10MB max per file
                    ->acceptedFileTypes([
                        'image/*',
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
                    ->visibility('private')
                    ->downloadable()
                    ->previewable(),

                // Internal flag (derived from type)
                Forms\Components\Hidden::make('is_internal')
                    ->dehydrateStateUsing(fn ($get) => 
                        $get('type') === TicketMessageType::INTERNAL_NOTE->value
                    ),

                // User ID (auto-filled with current user)
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),
            ]);
    }

    /**
     * Define the table schema for listing messages.
     *
     * @param Table $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('message')
            ->columns([
                // Message type badge
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon()),

                // Author name
                Tables\Columns\TextColumn::make('user.name')
                    ->label('From')
                    ->searchable()
                    ->sortable(),

                // Message content preview
                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->limit(60)
                    ->wrap()
                    ->searchable()
                    ->tooltip(fn ($record) => $record->message),

                // Attachments indicator
                Tables\Columns\IconColumn::make('has_attachments')
                    ->label('Files')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('')
                    ->alignCenter()
                    ->tooltip(fn ($record) => $record->has_attachments ? 
                        $record->attachments_count . ' file(s) attached' : null
                    ),

                // Internal flag
                Tables\Columns\IconColumn::make('is_internal')
                    ->label('Internal')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('')
                    ->alignCenter()
                    ->tooltip('Internal notes are not visible to customers'),

                // Read status
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->alignCenter(),

                // Created at
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Posted')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('M d, Y H:i:s')),
            ])
            ->filters([
                // Message type filter
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options(TicketMessageType::toSelectArray())
                    ->multiple(),

                // Internal messages filter
                Tables\Filters\TernaryFilter::make('is_internal')
                    ->label('Internal Only')
                    ->placeholder('All Messages')
                    ->trueLabel('Internal Notes')
                    ->falseLabel('Customer Visible'),

                // Unread filter
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Read Status')
                    ->placeholder('All Messages')
                    ->trueLabel('Read')
                    ->falseLabel('Unread'),
            ])
            ->headerActions([
                // Create new message action
                Tables\Actions\CreateAction::make()
                    ->label('Add Message')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Set the user_id to current authenticated user
                        $data['user_id'] = Auth::id();
                        
                        // Set is_internal based on type
                        $data['is_internal'] = $data['type'] === TicketMessageType::INTERNAL_NOTE->value;
                        
                        // Process attachments if any
                        if (!empty($data['attachments'])) {
                            $attachmentData = [];
                            foreach ($data['attachments'] as $path) {
                                $file = Storage::disk('private')->get($path);
                                $attachmentData[] = [
                                    'path' => $path,
                                    'original_name' => basename($path),
                                    'mime_type' => Storage::disk('private')->mimeType($path),
                                    'size' => Storage::disk('private')->size($path),
                                    'uploaded_at' => now()->toIso8601String(),
                                ];
                            }
                            $data['attachments'] = $attachmentData;
                        }
                        
                        return $data;
                    })
                    ->after(function ($record) {
                        // Mark as unread for customer to see new staff reply
                        if ($record->type === TicketMessageType::STAFF_REPLY) {
                            $record->update(['is_read' => false]);
                        }
                    }),
            ])
            ->actions([
                // View/expand message
                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn ($record) => 'Message from ' . $record->user->name)
                    ->modalContent(fn ($record) => view('filament.ticket-message-view', [
                        'record' => $record
                    ])),

                // Edit message
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => 
                        $record->user_id === Auth::id() && 
                        $record->created_at->diffInMinutes() < 30 // Can only edit within 30 mins
                    ),

                // Mark as read/unread
                Tables\Actions\Action::make('toggle_read')
                    ->label(fn ($record) => $record->is_read ? 'Mark Unread' : 'Mark Read')
                    ->icon(fn ($record) => $record->is_read ? 'heroicon-o-envelope' : 'heroicon-o-envelope-open')
                    ->action(fn ($record) => 
                        $record->is_read ? $record->markAsUnread() : $record->markAsRead()
                    ),

                // Delete message
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => 
                        $record->user_id === Auth::id() || 
                        Auth::user()->hasRole('super_admin')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Mark as read
                    Tables\Actions\BulkAction::make('mark_read')
                        ->label('Mark as Read')
                        ->icon('heroicon-o-envelope-open')
                        ->action(fn ($records) => $records->each->markAsRead()),

                    // Delete messages
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'asc') // Show oldest first for conversation flow
            ->poll('15s'); // Auto-refresh every 15 seconds for real-time updates
    }

    /**
     * Modify the query for loading messages.
     * 
     * @param Builder $query
     * @return Builder
     */
    protected function getTableQuery(): ?Builder
    {
        return parent::getTableQuery()
            ->with(['user']); // Eager load user relationship
    }
}
