<?php

namespace App\Filament\Admin\Resources\TicketResource\Pages;

use App\Filament\Admin\Resources\TicketResource;
use App\Models\TicketStatus;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil'),

            Actions\Action::make('assign_to_me')
                ->label(__('ticket_admin.action_assign_to_me'))
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->assignTo(Auth::id());
                    $this->record->refresh();

                    Notification::make()
                        ->title(__('ticket_admin.notif_assigned'))
                        ->body(__('ticket_admin.notif_assigned_body'))
                        ->success()
                        ->send();
                })
                ->visible(fn() => $this->record->assigned_to !== Auth::id()),

            Actions\Action::make('change_status')
                ->label(__('ticket_admin.action_change_status'))
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->form([
                    Forms\Components\Select::make('status')
                        ->label(__('ticket_admin.new_status'))
                        ->options(function () {
                            $allowed = $this->record->status->getAllowedTransitions();
                            return collect($allowed)->mapWithKeys(fn($status) => [
                                $status->value => $status->getLabel()
                            ])->all();
                        })
                        ->required()
                        ->native(false),

                    Forms\Components\Textarea::make('note')
                        ->label(__('ticket_admin.status_note'))
                        ->placeholder(__('ticket_admin.status_note_placeholder'))
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $oldStatus = $this->record->status;
                    $newStatus = TicketStatus::from($data['status']);

                    if (!$oldStatus->canTransitionTo($newStatus)) {
                        Notification::make()
                            ->title(__('ticket_admin.notif_invalid_transition'))
                            ->body(__('ticket_admin.notif_invalid_transition_body', [
                                'old' => $oldStatus->getLabel(),
                                'new' => $newStatus->getLabel(),
                            ]))
                            ->danger()
                            ->send();
                        return;
                    }

                    $this->record->update(['status' => $newStatus]);

                    if (!empty($data['note'])) {
                        $this->record->addMessage(
                            __('ticket_admin.status_changed_note', [
                                'old'  => $oldStatus->getLabel(),
                                'new'  => $newStatus->getLabel(),
                                'note' => $data['note'],
                            ]),
                            Auth::id(),
                            \App\Models\TicketMessageType::STATUS_CHANGE
                        );
                    }

                    $this->record->refresh();

                    Notification::make()
                        ->title(__('ticket_admin.notif_status_updated'))
                        ->body(__('ticket_admin.notif_status_updated_body', ['status' => $newStatus->getLabel()]))
                        ->success()
                        ->send();
                }),

            Actions\Action::make('resolve')
                ->label(__('ticket_admin.action_resolve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Textarea::make('resolution')
                        ->label(__('ticket_admin.resolution'))
                        ->required()
                        ->rows(4)
                        ->placeholder(__('ticket_admin.resolution_placeholder')),
                ])
                ->action(function (array $data) {
                    $this->record->resolve($data['resolution']);
                    $this->record->refresh();

                    Notification::make()
                        ->title(__('ticket_admin.notif_resolved'))
                        ->success()
                        ->send();
                })
                ->visible(fn() => in_array($this->record->status, [
                    TicketStatus::OPEN,
                    TicketStatus::IN_PROGRESS,
                    TicketStatus::PENDING
                ])),

            Actions\Action::make('close')
                ->label(__('ticket_admin.action_close'))
                ->icon('heroicon-o-lock-closed')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription(__('ticket_admin.close_modal_desc'))
                ->action(function () {
                    $this->record->close();
                    $this->record->refresh();

                    Notification::make()
                        ->title(__('ticket_admin.notif_closed'))
                        ->success()
                        ->send();
                })
                ->visible(fn() => $this->record->canBeClosed()),

            Actions\Action::make('reopen')
                ->label(__('ticket_admin.action_reopen'))
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription(__('ticket_admin.reopen_modal_desc'))
                ->action(function () {
                    $this->record->update(['status' => TicketStatus::OPEN]);
                    $this->record->refresh();

                    Notification::make()
                        ->title(__('ticket_admin.notif_reopened'))
                        ->warning()
                        ->send();
                })
                ->visible(fn() => $this->record->canBeReopened()),

            Actions\Action::make('escalate')
                ->label(__('ticket_admin.action_escalate'))
                ->icon('heroicon-o-arrow-trending-up')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label(__('ticket_admin.escalation_reason'))
                        ->required()
                        ->rows(3)
                        ->placeholder(__('ticket_admin.escalation_placeholder')),
                ])
                ->action(function (array $data) {
                    $this->record->escalate();

                    $this->record->addMessage(
                        __('ticket_admin.status_changed_note', [
                            'old'  => $this->record->status->getLabel(),
                            'new'  => TicketStatus::ESCALATED->getLabel(),
                            'note' => $data['reason'],
                        ]),
                        Auth::id(),
                        \App\Models\TicketMessageType::INTERNAL_NOTE,
                        [],
                        true
                    );

                    $this->record->refresh();

                    Notification::make()
                        ->title(__('ticket_admin.notif_escalated'))
                        ->danger()
                        ->send();
                })
                ->visible(fn() => $this->record->status !== TicketStatus::ESCALATED),

            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('ticket_admin.view_section_overview'))
                    ->schema([
                        Infolists\Components\TextEntry::make('ticket_number')
                            ->label(__('ticket_admin.view_ticket_number'))
                            ->size('lg')
                            ->weight('bold')
                            ->copyable()
                            ->icon('heroicon-o-ticket'),

                        Infolists\Components\TextEntry::make('type')
                            ->label(__('ticket_admin.view_type'))
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state->getLabel())
                            ->color(fn ($state) => $state->getColor())
                            ->icon(fn ($state) => $state->getIcon()),

                        Infolists\Components\TextEntry::make('priority')
                            ->label(__('ticket_admin.view_priority'))
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state->getLabel())
                            ->color(fn ($state) => $state->getColor())
                            ->icon(fn ($state) => $state->getIcon()),

                        Infolists\Components\TextEntry::make('status')
                            ->label(__('ticket_admin.view_status'))
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state->getLabel())
                            ->color(fn ($state) => $state->getColor())
                            ->icon(fn ($state) => $state->getIcon()),

                        Infolists\Components\TextEntry::make('subject')
                            ->label(__('ticket_admin.view_subject'))
                            ->size('lg')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('description')
                            ->label(__('ticket_admin.view_description'))
                            ->columnSpanFull()
                            ->markdown(),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make(__('ticket_admin.view_section_assignment'))
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label(__('ticket_admin.view_customer'))
                            ->icon('heroicon-o-user'),

                        Infolists\Components\TextEntry::make('user.email')
                            ->label(__('ticket_admin.view_customer_email'))
                            ->icon('heroicon-o-envelope')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('assignedTo.name')
                            ->label(__('ticket_admin.view_assigned_to'))
                            ->placeholder(__('ticket_admin.unassigned'))
                            ->icon('heroicon-o-user-circle'),

                        Infolists\Components\TextEntry::make('booking.id')
                            ->label(__('ticket_admin.view_related_booking'))
                            ->formatStateUsing(fn ($state) => $state
                                ? __('ticket_admin.view_booking_ref', ['id' => $state])
                                : '—'
                            )
                            ->url(fn ($record) => $record->booking ?
                                route('filament.admin.resources.bookings.view', $record->booking) : null
                            ),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('ticket_admin.view_section_timeline'))
                    ->schema([
                        Infolists\Components\TextEntry::make('due_date')
                            ->label(__('ticket_admin.view_due_date'))
                            ->dateTime()
                            ->icon('heroicon-o-clock')
                            ->color(fn ($record) => $record->is_overdue ? 'danger' : 'gray'),

                        Infolists\Components\TextEntry::make('time_remaining')
                            ->label(__('ticket_admin.view_time_remaining'))
                            ->state(fn ($record) => $record->time_remaining ?? __('ticket_admin.view_na'))
                            ->color(fn ($record) => $record->is_overdue ? 'danger' : 'gray'),

                        Infolists\Components\TextEntry::make('first_response_at')
                            ->label(__('ticket_admin.view_first_response'))
                            ->dateTime()
                            ->placeholder(__('ticket_admin.view_no_response_yet')),

                        Infolists\Components\TextEntry::make('response_time')
                            ->label(__('ticket_admin.view_response_time'))
                            ->state(fn ($record) => $record->response_time
                                ? __('ticket_admin.view_hours', ['value' => round($record->response_time, 1)])
                                : __('ticket_admin.view_na')
                            ),

                        Infolists\Components\TextEntry::make('resolved_at')
                            ->label(__('ticket_admin.view_resolved_at'))
                            ->dateTime()
                            ->placeholder(__('ticket_admin.view_not_resolved')),

                        Infolists\Components\TextEntry::make('resolution_time')
                            ->label(__('ticket_admin.view_resolution_time'))
                            ->state(fn ($record) => $record->resolution_time
                                ? __('ticket_admin.view_hours', ['value' => round($record->resolution_time, 1)])
                                : __('ticket_admin.view_na')
                            ),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label(__('ticket_admin.view_created'))
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label(__('ticket_admin.view_last_updated'))
                            ->dateTime()
                            ->since(),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make(__('ticket_admin.view_section_resolution'))
                    ->schema([
                        Infolists\Components\TextEntry::make('resolution')
                            ->label(__('ticket_admin.view_resolution'))
                            ->markdown()
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('internal_notes')
                            ->label(__('ticket_admin.view_internal_notes'))
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->resolution || $record->internal_notes)
                    ->collapsible(),

                Infolists\Components\Section::make(__('ticket_admin.view_section_feedback'))
                    ->schema([
                        Infolists\Components\TextEntry::make('rating')
                            ->label(__('ticket_admin.view_rating'))
                            ->formatStateUsing(fn ($state) => str_repeat('⭐', $state ?? 0)),

                        Infolists\Components\TextEntry::make('feedback')
                            ->label(__('ticket_admin.view_feedback'))
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->rating || $record->feedback)
                    ->collapsible(),
            ]);
    }
}
