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

/**
 * View Ticket Page
 * 
 * Displays comprehensive ticket details with actions for status management,
 * assignment, and resolution. Includes timeline and conversation thread.
 * 
 * @package App\Filament\Admin\Resources\TicketResource\Pages
 * @version 1.0.0
 */
class ViewTicket extends ViewRecord
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = TicketResource::class;

    /**
     * Get the header actions for the page.
     * Provides quick actions for common ticket operations.
     * 
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            // Edit action
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil'),

            // Assign to me action
            Actions\Action::make('assign_to_me')
                ->label('Assign to Me')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->assignTo(Auth::id());
                    $this->record->refresh();

                    Notification::make()
                        ->title('Ticket Assigned')
                        ->body('Ticket has been assigned to you.')
                        ->success()
                        ->send();
                })
                ->visible(fn() => $this->record->assigned_to !== Auth::id()),

            // Change status action
            Actions\Action::make('change_status')
                ->label('Change Status')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->form([
                    Forms\Components\Select::make('status')
                        ->label('New Status')
                        ->options(function () {
                            // Show only allowed transitions
                            $allowed = $this->record->status->getAllowedTransitions();
                            return collect($allowed)->mapWithKeys(fn($status) => [
                                $status->value => $status->getLabel()
                            ])->all();
                        })
                        ->required()
                        ->native(false),

                    Forms\Components\Textarea::make('note')
                        ->label('Note')
                        ->placeholder('Optional note about this status change')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $oldStatus = $this->record->status;
                    $newStatus = TicketStatus::from($data['status']);

                    if (!$oldStatus->canTransitionTo($newStatus)) {
                        Notification::make()
                            ->title('Invalid Status Transition')
                            ->body('Cannot change status from ' . $oldStatus->getLabel() . ' to ' . $newStatus->getLabel())
                            ->danger()
                            ->send();
                        return;
                    }

                    $this->record->update(['status' => $newStatus]);

                    // Add status change message
                    if (!empty($data['note'])) {
                        $this->record->addMessage(
                            "Status changed from {$oldStatus->getLabel()} to {$newStatus->getLabel()}. Note: {$data['note']}",
                            Auth::id(),
                            \App\Models\TicketMessageType::STATUS_CHANGE
                        );
                    }

                    $this->record->refresh();

                    Notification::make()
                        ->title('Status Updated')
                        ->body('Ticket status changed to ' . $newStatus->getLabel())
                        ->success()
                        ->send();
                }),

            // Resolve action
            Actions\Action::make('resolve')
                ->label('Resolve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Textarea::make('resolution')
                        ->label('Resolution')
                        ->required()
                        ->rows(4)
                        ->placeholder('Describe how the issue was resolved'),
                ])
                ->action(function (array $data) {
                    $this->record->resolve($data['resolution']);
                    $this->record->refresh();

                    Notification::make()
                        ->title('Ticket Resolved')
                        ->success()
                        ->send();
                })
                ->visible(fn() => in_array($this->record->status, [
                    TicketStatus::OPEN,
                    TicketStatus::IN_PROGRESS,
                    TicketStatus::PENDING
                ])),

            // Close action
            Actions\Action::make('close')
                ->label('Close')
                ->icon('heroicon-o-lock-closed')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Are you sure you want to close this ticket? This action indicates the issue is fully resolved.')
                ->action(function () {
                    $this->record->close();
                    $this->record->refresh();

                    Notification::make()
                        ->title('Ticket Closed')
                        ->success()
                        ->send();
                })
                ->visible(fn() => $this->record->canBeClosed()),

            // Reopen action
            Actions\Action::make('reopen')
                ->label('Reopen')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription('Reopen this closed ticket?')
                ->action(function () {
                    $this->record->update(['status' => TicketStatus::OPEN]);
                    $this->record->refresh();

                    Notification::make()
                        ->title('Ticket Reopened')
                        ->warning()
                        ->send();
                })
                ->visible(fn() => $this->record->canBeReopened()),

            // Escalate action
            Actions\Action::make('escalate')
                ->label('Escalate')
                ->icon('heroicon-o-arrow-trending-up')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label('Escalation Reason')
                        ->required()
                        ->rows(3)
                        ->placeholder('Why is this ticket being escalated?'),
                ])
                ->action(function (array $data) {
                    $this->record->escalate();
                    
                    // Add escalation note
                    $this->record->addMessage(
                        "Ticket escalated. Reason: {$data['reason']}",
                        Auth::id(),
                        \App\Models\TicketMessageType::INTERNAL_NOTE,
                        [],
                        true
                    );

                    $this->record->refresh();

                    Notification::make()
                        ->title('Ticket Escalated')
                        ->danger()
                        ->send();
                })
                ->visible(fn() => $this->record->status !== TicketStatus::ESCALATED),

            // Delete action
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }

    /**
     * Build the infolist for displaying ticket details.
     * 
     * @param Infolist $infolist
     * @return Infolist
     */
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Ticket Overview Section
                Infolists\Components\Section::make('Ticket Overview')
                    ->schema([
                        Infolists\Components\TextEntry::make('ticket_number')
                            ->label('Ticket Number')
                            ->size('lg')
                            ->weight('bold')
                            ->copyable()
                            ->icon('heroicon-o-ticket'),

                        Infolists\Components\TextEntry::make('type')
                            ->label('Type')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state->getLabel())
                            ->color(fn ($state) => $state->getColor())
                            ->icon(fn ($state) => $state->getIcon()),

                        Infolists\Components\TextEntry::make('priority')
                            ->label('Priority')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state->getLabel())
                            ->color(fn ($state) => $state->getColor())
                            ->icon(fn ($state) => $state->getIcon()),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state->getLabel())
                            ->color(fn ($state) => $state->getColor())
                            ->icon(fn ($state) => $state->getIcon()),

                        Infolists\Components\TextEntry::make('subject')
                            ->label('Subject')
                            ->size('lg')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull()
                            ->markdown(),
                    ])
                    ->columns(4),

                // Customer & Assignment Section
                Infolists\Components\Section::make('Customer & Assignment')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Customer')
                            ->icon('heroicon-o-user'),

                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Customer Email')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('assignedTo.name')
                            ->label('Assigned To')
                            ->placeholder('Unassigned')
                            ->icon('heroicon-o-user-circle'),

                        Infolists\Components\TextEntry::make('booking.id')
                            ->label('Related Booking')
                            ->formatStateUsing(fn ($state) => $state ? "Booking #{$state}" : '—')
                            ->url(fn ($record) => $record->booking ? 
                                route('filament.admin.resources.bookings.view', $record->booking) : null
                            ),
                    ])
                    ->columns(2),

                // Timeline Section
                Infolists\Components\Section::make('Timeline & SLA')
                    ->schema([
                        Infolists\Components\TextEntry::make('due_date')
                            ->label('Due Date')
                            ->dateTime()
                            ->icon('heroicon-o-clock')
                            ->color(fn ($record) => $record->is_overdue ? 'danger' : 'gray'),

                        Infolists\Components\TextEntry::make('time_remaining')
                            ->label('Time Remaining')
                            ->state(fn ($record) => $record->time_remaining ?? 'N/A')
                            ->color(fn ($record) => $record->is_overdue ? 'danger' : 'gray'),

                        Infolists\Components\TextEntry::make('first_response_at')
                            ->label('First Response')
                            ->dateTime()
                            ->placeholder('No response yet'),

                        Infolists\Components\TextEntry::make('response_time')
                            ->label('Response Time')
                            ->state(fn ($record) => $record->response_time ? 
                                round($record->response_time, 1) . ' hours' : 'N/A'
                            ),

                        Infolists\Components\TextEntry::make('resolved_at')
                            ->label('Resolved At')
                            ->dateTime()
                            ->placeholder('Not resolved yet'),

                        Infolists\Components\TextEntry::make('resolution_time')
                            ->label('Resolution Time')
                            ->state(fn ($record) => $record->resolution_time ? 
                                round($record->resolution_time, 1) . ' hours' : 'N/A'
                            ),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime()
                            ->since(),
                    ])
                    ->columns(4),

                // Resolution Section
                Infolists\Components\Section::make('Resolution')
                    ->schema([
                        Infolists\Components\TextEntry::make('resolution')
                            ->label('Resolution')
                            ->markdown()
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('internal_notes')
                            ->label('Internal Notes')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->resolution || $record->internal_notes)
                    ->collapsible(),

                // Feedback Section
                Infolists\Components\Section::make('Customer Feedback')
                    ->schema([
                        Infolists\Components\TextEntry::make('rating')
                            ->label('Rating')
                            ->formatStateUsing(fn ($state) => str_repeat('⭐', $state ?? 0)),

                        Infolists\Components\TextEntry::make('feedback')
                            ->label('Feedback')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->rating || $record->feedback)
                    ->collapsible(),
            ]);
    }
}
