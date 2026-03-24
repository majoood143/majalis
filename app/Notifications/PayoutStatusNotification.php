<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\PayoutStatus;
use App\Models\OwnerPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected OwnerPayout $payout) {}

    /**
     * Deliver via in-app (database) and email.
     */
    public function via(mixed $notifiable): array
    {
        return ['database', 'mail'];
    }

    // ──────────────────────────────────────────────────────────────
    // EMAIL
    // ──────────────────────────────────────────────────────────────

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject($this->getSubject())
            ->view('emails.payout.status-updated', [
                'payout' => $this->payout,
                'owner'  => $notifiable,
                'title'  => $this->getTitle(),
                'body'   => $this->getBody(),
                'locale' => app()->getLocale(),
            ]);
    }

    // ──────────────────────────────────────────────────────────────
    // IN-APP (Filament database notifications bell)
    // ──────────────────────────────────────────────────────────────

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'title'     => $this->getTitle(),
            'body'      => $this->getBody(),
            'color'     => $this->payout->status->getColor(),
            'icon'      => $this->payout->status->getIcon(),
            'iconColor' => $this->payout->status->getColor(),
            'actions'   => [],
            'duration'  => 'persistent',
            'format'    => 'filament',
        ];
    }

    public function toArray(mixed $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    // ──────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────

    protected function getSubject(): string
    {
        return match ($this->payout->status) {
            PayoutStatus::PROCESSING => __('owner.payouts.notif_processing_subject', ['number' => $this->payout->payout_number]),
            PayoutStatus::COMPLETED  => __('owner.payouts.notif_completed_subject',  ['number' => $this->payout->payout_number]),
            PayoutStatus::FAILED     => __('owner.payouts.notif_failed_subject',     ['number' => $this->payout->payout_number]),
            PayoutStatus::ON_HOLD    => __('owner.payouts.notif_on_hold_subject',    ['number' => $this->payout->payout_number]),
            PayoutStatus::CANCELLED  => __('owner.payouts.notif_cancelled_subject',  ['number' => $this->payout->payout_number]),
            default                  => __('owner.payouts.notif_updated_subject',    ['number' => $this->payout->payout_number]),
        };
    }

    protected function getTitle(): string
    {
        return match ($this->payout->status) {
            PayoutStatus::PROCESSING => __('owner.payouts.notif_processing_title'),
            PayoutStatus::COMPLETED  => __('owner.payouts.notif_completed_title'),
            PayoutStatus::FAILED     => __('owner.payouts.notif_failed_title'),
            PayoutStatus::ON_HOLD    => __('owner.payouts.notif_on_hold_title'),
            PayoutStatus::CANCELLED  => __('owner.payouts.notif_cancelled_title'),
            default                  => __('owner.payouts.notif_updated_title'),
        };
    }

    protected function getBody(): string
    {
        return match ($this->payout->status) {
            PayoutStatus::PROCESSING => __('owner.payouts.notif_processing_body', [
                'number' => $this->payout->payout_number,
            ]),
            PayoutStatus::COMPLETED  => __('owner.payouts.notif_completed_body', [
                'number' => $this->payout->payout_number,
                'amount' => number_format((float) $this->payout->net_payout, 3) . ' OMR',
            ]),
            PayoutStatus::FAILED     => __('owner.payouts.notif_failed_body', [
                'number' => $this->payout->payout_number,
                'reason' => $this->payout->failure_reason ?? '-',
            ]),
            PayoutStatus::ON_HOLD    => __('owner.payouts.notif_on_hold_body', [
                'number' => $this->payout->payout_number,
            ]),
            PayoutStatus::CANCELLED  => __('owner.payouts.notif_cancelled_body', [
                'number' => $this->payout->payout_number,
            ]),
            default                  => __('owner.payouts.notif_updated_body', [
                'number' => $this->payout->payout_number,
            ]),
        };
    }
}
