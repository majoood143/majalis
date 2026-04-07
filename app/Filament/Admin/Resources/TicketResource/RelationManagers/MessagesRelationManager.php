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

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $recordTitleAttribute = 'message';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('ticket_admin.msg_col_message') === 'msg_col_message'
            ? 'Conversation'
            : __('ticket_admin.msg_add_heading');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ticket_admin.msg_add');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label(__('ticket_admin.msg_type'))
                    ->options([
                        TicketMessageType::STAFF_REPLY->value    => TicketMessageType::STAFF_REPLY->getLabel(),
                        TicketMessageType::INTERNAL_NOTE->value  => TicketMessageType::INTERNAL_NOTE->getLabel(),
                    ])
                    ->default(TicketMessageType::STAFF_REPLY->value)
                    ->required()
                    ->native(false)
                    ->helperText(__('ticket_admin.msg_type_help'))
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('is_internal', $state === TicketMessageType::INTERNAL_NOTE->value);
                    }),

                Forms\Components\Textarea::make('message')
                    ->label(__('ticket_admin.msg_message'))
                    ->required()
                    ->rows(6)
                    ->columnSpanFull()
                    ->placeholder(__('ticket_admin.msg_message_placeholder'))
                    ->maxLength(65535),

                Forms\Components\FileUpload::make('attachments')
                    ->label(__('ticket_admin.msg_attachments'))
                    ->multiple()
                    ->directory('ticket-attachments')
                    ->disk('private')
                    ->maxSize(10240)
                    ->maxFiles(5)
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
                    ->helperText(__('ticket_admin.msg_attachments_help'))
                    ->columnSpanFull()
                    ->visibility('private')
                    ->downloadable()
                    ->previewable()
                    ->reorderable()
                    ->afterStateHydrated(function ($component, $state) {
                        if (is_array($state) && !empty($state)) {
                            $first = reset($state);
                            if (is_array($first) && isset($first['path'])) {
                                $component->state(array_column($state, 'path'));
                            }
                        }
                    }),

                Forms\Components\Hidden::make('is_internal')
                    ->default(false)
                    ->dehydrateStateUsing(fn ($get) =>
                        $get('type') === TicketMessageType::INTERNAL_NOTE->value
                    ),

                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('message')
            ->columns([
                Tables\Columns\BadgeColumn::make('type')
                    ->label(__('ticket_admin.msg_col_type'))
                    ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'Unknown')
                    ->color(fn ($state) => $state?->getColor() ?? 'gray')
                    ->icon(fn ($state) => $state?->getIcon() ?? 'heroicon-o-question-mark-circle')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('ticket_admin.msg_col_from'))
                    ->searchable()
                    ->sortable()
                    ->default('Unknown User')
                    ->description(fn ($record) => $record->user?->email)
                    ->color(fn ($record) =>
                        $record->is_internal ? 'warning' : 'primary'
                    ),

                Tables\Columns\TextColumn::make('message')
                    ->label(__('ticket_admin.msg_col_message'))
                    ->limit(60)
                    ->wrap()
                    ->searchable()
                    ->tooltip(fn ($record) => $record->message)
                    ->html()
                    ->extraAttributes(['class' => 'prose prose-sm']),

                Tables\Columns\IconColumn::make('has_attachments')
                    ->label(__('ticket_admin.msg_col_files'))
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('primary')
                    ->falseColor('gray')
                    ->alignCenter()
                    ->tooltip(fn ($record) =>
                        $record->has_attachments
                            ? __('ticket_admin.msg_files_attached', ['count' => $record->attachments_count])
                            : __('ticket_admin.msg_no_attachments')
                    ),

                Tables\Columns\IconColumn::make('is_internal')
                    ->label(__('ticket_admin.msg_col_internal'))
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-eye')
                    ->alignCenter()
                    ->tooltip(fn ($record) =>
                        $record->is_internal
                            ? __('ticket_admin.msg_internal_note')
                            : __('ticket_admin.msg_visible_to_customers')
                    ),

                Tables\Columns\IconColumn::make('is_read')
                    ->label(__('ticket_admin.msg_col_read'))
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->alignCenter()
                    ->tooltip(fn ($record) =>
                        $record->is_read
                            ? __('ticket_admin.msg_is_read')
                            : __('ticket_admin.msg_is_unread')
                    ),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('ticket_admin.msg_col_posted'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('M d, Y H:i:s'))
                    ->description(fn ($record) =>
                        $record->updated_at->ne($record->created_at)
                            ? 'Edited ' . $record->updated_at->diffForHumans()
                            : null
                    ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('ticket_admin.msg_filter_type'))
                    ->options(TicketMessageType::toSelectArray())
                    ->multiple()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_internal')
                    ->label(__('ticket_admin.msg_filter_visibility'))
                    ->placeholder(__('ticket_admin.msg_filter_all'))
                    ->trueLabel(__('ticket_admin.msg_filter_internal_only'))
                    ->falseLabel(__('ticket_admin.msg_filter_customer_only'))
                    ->queries(
                        true:  fn (Builder $query) => $query->where('is_internal', true),
                        false: fn (Builder $query) => $query->where('is_internal', false),
                        blank: fn (Builder $query) => $query,
                    ),

                Tables\Filters\TernaryFilter::make('is_read')
                    ->label(__('ticket_admin.msg_filter_read_status'))
                    ->placeholder(__('ticket_admin.msg_filter_all'))
                    ->trueLabel(__('ticket_admin.msg_filter_read'))
                    ->falseLabel(__('ticket_admin.msg_filter_unread'))
                    ->queries(
                        true:  fn (Builder $query) => $query->where('is_read', true),
                        false: fn (Builder $query) => $query->where('is_read', false),
                        blank: fn (Builder $query) => $query,
                    ),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('ticket_admin.msg_filter_author'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('ticket_admin.msg_add'))
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->modalHeading(__('ticket_admin.msg_add_heading'))
                    ->modalWidth('2xl')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id']     = Auth::id();
                        $data['is_internal'] = $data['type'] === TicketMessageType::INTERNAL_NOTE->value;

                        if (!empty($data['attachments'])) {
                            $attachmentData = [];

                            foreach ($data['attachments'] as $path) {
                                $attachmentData[] = [
                                    'path'          => $path,
                                    'original_name' => basename($path),
                                    'size'          => Storage::disk('private')->size($path),
                                    'uploaded_at'   => now()->toIso8601String(),
                                ];
                            }

                            $data['attachments'] = $attachmentData;
                        }

                        return $data;
                    })
                    ->after(function ($record) {
                        if ($record->type === TicketMessageType::STAFF_REPLY) {
                            $record->update(['is_read' => false]);
                        }

                        $record->ticket->touch();
                    })
                    ->successNotificationTitle(__('ticket_admin.msg_add_success')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn ($record) => __('ticket_admin.msg_modal_from', [
                        'name' => $record->user?->name ?? 'Unknown User',
                    ]))
                    ->modalContent(fn ($record) => view('filament.ticket-message-view', [
                        'record' => $record
                    ]))
                    ->modalWidth('3xl')
                    ->slideOver(),

                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) =>
                        $record->user_id === Auth::id() &&
                        $record->created_at->diffInMinutes(now()) < 30
                    )
                    ->modalHeading(__('ticket_admin.msg_edit_heading'))
                    ->modalWidth('2xl')
                    ->successNotificationTitle(__('ticket_admin.msg_edit_success')),

                Tables\Actions\Action::make('toggle_read')
                    ->label(fn ($record) => $record->is_read
                        ? __('ticket_admin.msg_mark_unread')
                        : __('ticket_admin.msg_mark_read')
                    )
                    ->icon(fn ($record) =>
                        $record->is_read
                            ? 'heroicon-o-envelope'
                            : 'heroicon-o-envelope-open'
                    )
                    ->color(fn ($record) => $record->is_read ? 'gray' : 'primary')
                    ->action(function ($record) {
                        $record->is_read
                            ? $record->markAsUnread()
                            : $record->markAsRead();
                    })
                    ->requiresConfirmation(false)
                    ->successNotificationTitle(fn ($record) =>
                        $record->is_read
                            ? __('ticket_admin.msg_marked_read')
                            : __('ticket_admin.msg_marked_unread')
                    ),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) =>
                        $record->user_id === Auth::id() ||
                        Auth::user()->hasRole('super_admin')
                    )
                    ->requiresConfirmation()
                    ->modalHeading(__('ticket_admin.msg_delete_heading'))
                    ->modalDescription(__('ticket_admin.msg_delete_desc'))
                    ->successNotificationTitle(__('ticket_admin.msg_delete_success')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_read')
                        ->label(__('ticket_admin.msg_bulk_mark_read'))
                        ->icon('heroicon-o-envelope-open')
                        ->color('success')
                        ->action(fn ($records) => $records->each->markAsRead())
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle(__('ticket_admin.msg_bulk_marked_read')),

                    Tables\Actions\BulkAction::make('mark_unread')
                        ->label(__('ticket_admin.msg_bulk_mark_unread'))
                        ->icon('heroicon-o-envelope')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->markAsUnread())
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle(__('ticket_admin.msg_bulk_marked_unread')),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(__('ticket_admin.msg_bulk_delete_heading'))
                        ->modalDescription(__('ticket_admin.msg_bulk_delete_confirm'))
                        ->successNotificationTitle(__('ticket_admin.msg_bulk_deleted')),
                ]),
            ])
            ->defaultSort('created_at', 'asc')
            ->poll('15s')
            ->striped()
            ->persistFiltersInSession()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }

    protected function modifyTableQuery(Builder $query): Builder
    {
        return $query
            ->with(['user:id,name,email'])
            ->withCount('attachments')
            ->orderBy('created_at', 'asc');
    }

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->exists;
    }
}
