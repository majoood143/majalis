<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PayoutResource\Pages;

use App\Enums\PayoutStatus;
use App\Filament\Owner\Resources\PayoutResource;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * ViewPayout Page for Owner Panel
 *
 * Displays detailed view of a single payout with action capabilities.
 * Includes modal-based issue reporting to avoid route dependency issues.
 *
 * Features:
 * - Download receipt (for completed payouts)
 * - Report issue via modal form (creates support ticket)
 * - Back to list navigation
 *
 * @package App\Filament\Owner\Resources\PayoutResource\Pages
 * @version 2.0.0
 *
 * @property-read \App\Models\OwnerPayout $record
 */
class ViewPayout extends ViewRecord
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = PayoutResource::class;

    /**
     * Get the page title.
     *
     * Uses localized string with payout number interpolation.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return __('owner.payouts.view_title', [
            'number' => $this->record->payout_number,
        ]);
    }

    /**
     * Get the page heading.
     *
     * Displays the payout number as the main heading.
     *
     * @return string
     */
    public function getHeading(): string
    {
        return $this->record->payout_number;
    }

    /**
     * Get the page subheading.
     *
     * Displays period, amount, and status summary.
     * Uses explicit float casting for PHP 8.4 strict types compatibility.
     *
     * @return string|null
     */
    public function getSubheading(): ?string
    {
        return __('owner.payouts.view_subheading', [
            'period' => $this->record->period_start->format('M d') . ' - ' .
                $this->record->period_end->format('M d, Y'),
            'amount' => number_format((float) $this->record->net_payout, 3),
            'status' => $this->record->status->getLabel(),
        ]);
    }

    /**
     * Get header actions.
     *
     * Provides contextual actions based on payout status:
     * - Download receipt (visible only for completed payouts with receipts)
     * - Report issue (visible only for completed payouts, uses modal form)
     * - Back to list (always visible)
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // ====================================================
            // Download Receipt Action
            // ====================================================
            // Only visible when payout is completed AND has a receipt
            Actions\Action::make('downloadReceipt')
                ->label(__('owner.payouts.download_receipt'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->visible(fn (): bool =>
                    $this->record->status === PayoutStatus::COMPLETED
                    && !empty($this->record->receipt_path)
                )
                ->action(function (): void {
                    // Verify receipt file exists before redirecting
                    if (Storage::disk('public')->exists($this->record->receipt_path)) {
                        // Redirect to the public URL for download
                        redirect(Storage::disk('public')->url($this->record->receipt_path));
                    } else {
                        // Show warning notification if file is missing
                        Notification::make()
                            ->warning()
                            ->title(__('owner.payouts.receipt_not_found'))
                            ->body(__('owner.payouts.receipt_not_found_body'))
                            ->send();
                    }
                }),

            // ====================================================
            // Report Issue Action (Modal Form)
            // ====================================================
            // Uses modal form instead of external route to avoid
            // dependency on non-existent TicketResource in Owner panel
            Actions\Action::make('reportIssue')
                ->label(__('owner.payouts.report_issue'))
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->visible(fn (): bool => $this->record->status === PayoutStatus::COMPLETED)
                ->modalHeading(__('owner.payouts.report_issue_modal_title'))
                ->modalDescription(__('owner.payouts.report_issue_modal_description', [
                    'payout' => $this->record->payout_number,
                ]))
                ->modalSubmitActionLabel(__('owner.payouts.submit_issue'))
                ->modalWidth('lg')
                ->form([
                    // Issue Type Selection
                    Forms\Components\Select::make('type')
                        ->label(__('owner.payouts.issue_type'))
                        ->options([
                            TicketType::CLAIM->value => __('owner.payouts.issue_types.claim'),
                            TicketType::COMPLAINT->value => __('owner.payouts.issue_types.complaint'),
                            TicketType::INQUIRY->value => __('owner.payouts.issue_types.inquiry'),
                        ])
                        ->default(TicketType::CLAIM->value)
                        ->required()
                        ->native(false),

                    // Priority Selection
                    Forms\Components\Select::make('priority')
                        ->label(__('owner.payouts.issue_priority'))
                        ->options([
                            TicketPriority::LOW->value => __('owner.payouts.priorities.low'),
                            TicketPriority::MEDIUM->value => __('owner.payouts.priorities.medium'),
                            TicketPriority::HIGH->value => __('owner.payouts.priorities.high'),
                        ])
                        ->default(TicketPriority::MEDIUM->value)
                        ->required()
                        ->native(false),

                    // Subject (Pre-filled with payout reference)
                    Forms\Components\TextInput::make('subject')
                        ->label(__('owner.payouts.issue_subject'))
                        ->default(fn (): string => __('owner.payouts.default_subject', [
                            'payout' => $this->record->payout_number,
                        ]))
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull(),

                    // Description
                    Forms\Components\Textarea::make('description')
                        ->label(__('owner.payouts.issue_description'))
                        ->placeholder(__('owner.payouts.issue_description_placeholder'))
                        ->required()
                        ->minLength(10)
                        ->maxLength(2000)
                        ->rows(5)
                        ->columnSpanFull(),

                    // Hidden payout context (displayed as info)
                    Forms\Components\Placeholder::make('payout_info')
                        ->label(__('owner.payouts.related_payout'))
                        ->content(fn (): string => sprintf(
                            '%s - %s OMR (%s)',
                            $this->record->payout_number,
                            number_format((float) $this->record->net_payout, 3),
                            $this->record->status->getLabel()
                        ))
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    $this->createSupportTicket($data);
                }),

            // ====================================================
            // Back to List Action
            // ====================================================
            Actions\Action::make('backToList')
                ->label(__('owner.payouts.back_to_list'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(PayoutResource::getUrl('index')),
        ];
    }

    /**
     * Create a support ticket from the modal form data.
     *
     * Creates a new Ticket record linked to the current user with
     * payout-specific metadata. Uses database transaction for safety.
     *
     * @param array<string, mixed> $data Form data from modal
     * @return void
     */
    protected function createSupportTicket(array $data): void
    {
        try {
            DB::beginTransaction();

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Create the support ticket
            $ticket = Ticket::create([
                'user_id' => $user->id,
                'type' => $data['type'],
                'priority' => $data['priority'],
                'status' => TicketStatus::OPEN->value,
                'subject' => $data['subject'],
                'description' => $data['description'],
                // Store payout reference in metadata for admin reference
                'metadata' => [
                    'source' => 'payout_issue',
                    'payout_id' => $this->record->id,
                    'payout_number' => $this->record->payout_number,
                    'payout_amount' => (float) $this->record->net_payout,
                    'payout_status' => $this->record->status->value,
                    'payout_period' => sprintf(
                        '%s - %s',
                        $this->record->period_start->format('Y-m-d'),
                        $this->record->period_end->format('Y-m-d')
                    ),
                    'reported_at' => now()->toIso8601String(),
                ],
            ]);

            // Add initial message to ticket
            if (method_exists($ticket, 'addMessage')) {
                $ticket->addMessage(
                    $data['description'],
                    $user->id,
                    \App\Models\TicketMessageType::CUSTOMER_REPLY ?? 'customer_reply',
                    [] // No attachments from this form
                );
            }

            DB::commit();

            // Success notification with ticket reference
            Notification::make()
                ->success()
                ->title(__('owner.payouts.issue_submitted'))
                ->body(__('owner.payouts.issue_submitted_body', [
                    'ticket' => $ticket->ticket_number,
                ]))
                ->persistent()
                ->actions([
                    // Optional: Add action to view ticket if route exists
                    \Filament\Notifications\Actions\Action::make('view_ticket')
                        ->label(__('owner.payouts.view_ticket'))
                        ->url($this->getTicketViewUrl($ticket))
                        ->visible(fn (): bool => $this->hasCustomerTicketRoute()),
                ])
                ->send();

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('Failed to create payout issue ticket', [
                'payout_id' => $this->record->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Error notification
            Notification::make()
                ->danger()
                ->title(__('owner.payouts.issue_failed'))
                ->body(__('owner.payouts.issue_failed_body'))
                ->send();
        }
    }

    /**
     * Check if customer ticket route exists.
     *
     * @return bool
     */
    protected function hasCustomerTicketRoute(): bool
    {
        return \Illuminate\Support\Facades\Route::has('customer.tickets.show');
    }

    /**
     * Get the URL to view the ticket.
     *
     * Falls back gracefully if route doesn't exist.
     *
     * @param Ticket $ticket
     * @return string|null
     */
    protected function getTicketViewUrl(Ticket $ticket): ?string
    {
        if ($this->hasCustomerTicketRoute()) {
            return route('customer.tickets.show', $ticket);
        }

        return null;
    }

    /**
     * Get the breadcrumb label.
     *
     * @return string
     */
    public function getBreadcrumb(): string
    {
        return $this->record->payout_number;
    }
}
