<?php

namespace App\Filament\Admin\Resources\TicketResource\RelationManagers;

use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use App\Models\TicketMessage;
use App\Models\TicketMessageType;
use Filament\Forms;
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

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('ticket_admin.msg_col_message') === 'msg_col_message'
            ? 'Conversation'
            : __('ticket_admin.msg_add_heading');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ticket_admin.msg_add');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
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

                Textarea::make('message')
                    ->label(__('ticket_admin.msg_message'))
                    ->required()
                    ->rows(6)
                    ->columnSpanFull()
                    ->placeholder(__('ticket_admin.msg_message_placeholder'))
                    ->maxLength(65535),

                FileUpload::make('attachments')
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

                Hidden::make('is_internal')
                    ->default(false)
                    ->dehydrateStateUsing(fn ($get) =>
                        $get('type') === TicketMessageType::INTERNAL_NOTE->value
                    ),

                Hidden::make('user_id')
                    ->default(Auth::id())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('message')
            ->columns([
                BadgeColumn::make('type')
                    ->label(__('ticket_admin.msg_col_type'))
                    ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'Unknown')
                    ->color(fn ($state) => $state?->getColor() ?? 'gray')
                    ->icon(fn ($state) => $state?->getIcon() ?? 'heroicon-o-question-mark-circle')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(__('ticket_admin.msg_col_from'))
                    ->searchable()
                    ->sortable()
                    ->default('Unknown User')
                    ->description(fn ($record) => $record->user?->email)
                    ->color(fn ($record) =>
                        $record->is_internal ? 'warning' : 'primary'
                    ),

                TextColumn::make('message')
                    ->label(__('ticket_admin.msg_col_message'))
                    ->limit(60)
                    ->wrap()
                    ->searchable()
                    ->tooltip(fn ($record) => $record->message)
                    ->html()
                    ->extraAttributes(['class' => 'prose prose-sm']),

                IconColumn::make('has_attachments')
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

                IconColumn::make('is_internal')
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

                IconColumn::make('is_read')
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

                TextColumn::make('created_at')
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
                SelectFilter::make('type')
                    ->label(__('ticket_admin.msg_filter_type'))
                    ->options(TicketMessageType::toSelectArray())
                    ->multiple()
                    ->preload(),

                TernaryFilter::make('is_internal')
                    ->label(__('ticket_admin.msg_filter_visibility'))
                    ->placeholder(__('ticket_admin.msg_filter_all'))
                    ->trueLabel(__('ticket_admin.msg_filter_internal_only'))
                    ->falseLabel(__('ticket_admin.msg_filter_customer_only'))
                    ->queries(
                        true:  fn (Builder $query) => $query->where('is_internal', true),
                        false: fn (Builder $query) => $query->where('is_internal', false),
                        blank: fn (Builder $query) => $query,
                    ),

                TernaryFilter::make('is_read')
                    ->label(__('ticket_admin.msg_filter_read_status'))
                    ->placeholder(__('ticket_admin.msg_filter_all'))
                    ->trueLabel(__('ticket_admin.msg_filter_read'))
                    ->falseLabel(__('ticket_admin.msg_filter_unread'))
                    ->queries(
                        true:  fn (Builder $query) => $query->where('is_read', true),
                        false: fn (Builder $query) => $query->where('is_read', false),
                        blank: fn (Builder $query) => $query,
                    ),

                SelectFilter::make('user_id')
                    ->label(__('ticket_admin.msg_filter_author'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('ticket_admin.msg_add'))
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->modalHeading(__('ticket_admin.msg_add_heading'))
                    ->modalWidth('2xl')
                    ->mutateDataUsing(function (array $data): array {
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
            ->recordActions([
                ViewAction::make()
                    ->modalHeading(fn ($record) => __('ticket_admin.msg_modal_from', [
                        'name' => $record->user?->name ?? 'Unknown User',
                    ]))
                    ->modalContent(fn ($record) => view('filament.ticket-message-view', [
                        'record' => $record
                    ]))
                    ->modalWidth('3xl')
                    ->slideOver(),

                EditAction::make()
                    ->visible(fn ($record) =>
                        $record->user_id === Auth::id() &&
                        $record->created_at->diffInMinutes(now()) < 30
                    )
                    ->modalHeading(__('ticket_admin.msg_edit_heading'))
                    ->modalWidth('2xl')
                    ->successNotificationTitle(__('ticket_admin.msg_edit_success')),

                Action::make('toggle_read')
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

                DeleteAction::make()
                    ->visible(fn ($record) =>
                        $record->user_id === Auth::id() ||
                        Auth::user()->hasRole('super_admin')
                    )
                    ->requiresConfirmation()
                    ->modalHeading(__('ticket_admin.msg_delete_heading'))
                    ->modalDescription(__('ticket_admin.msg_delete_desc'))
                    ->successNotificationTitle(__('ticket_admin.msg_delete_success')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_read')
                        ->label(__('ticket_admin.msg_bulk_mark_read'))
                        ->icon('heroicon-o-envelope-open')
                        ->color('success')
                        ->action(fn ($records) => $records->each->markAsRead())
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle(__('ticket_admin.msg_bulk_marked_read')),

                    BulkAction::make('mark_unread')
                        ->label(__('ticket_admin.msg_bulk_mark_unread'))
                        ->icon('heroicon-o-envelope')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->markAsUnread())
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle(__('ticket_admin.msg_bulk_marked_unread')),

                    DeleteBulkAction::make()
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

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->exists;
    }
}
