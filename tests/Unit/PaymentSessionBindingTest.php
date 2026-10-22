<?php

namespace Tests\Unit;

use App\Models\Payment;
use App\Services\PaymentService;
use Tests\TestCase;

/**
 * A Thawani session may only confirm the payment it was created for —
 * otherwise a session paid for a cheap booking could confirm an expensive one.
 */
class PaymentSessionBindingTest extends TestCase
{
    private function payment(string $reference = 'PAY-1-ABC', float $amount = 150.5): Payment
    {
        return (new Payment())->forceFill([
            'payment_reference' => $reference,
            'amount' => $amount,
        ]);
    }

    private function service(): PaymentService
    {
        return app(PaymentService::class);
    }

    public function test_matching_session_is_accepted(): void
    {
        $this->assertTrue($this->service()->sessionMatchesPayment(
            ['client_reference_id' => 'PAY-1-ABC', 'total_amount' => 150500],
            $this->payment(),
        ));
    }

    public function test_session_for_another_payment_is_rejected(): void
    {
        $this->assertFalse($this->service()->sessionMatchesPayment(
            ['client_reference_id' => 'PAY-2-XYZ', 'total_amount' => 150500],
            $this->payment(),
        ));
    }

    public function test_session_without_reference_is_rejected(): void
    {
        $this->assertFalse($this->service()->sessionMatchesPayment(
            ['total_amount' => 150500],
            $this->payment(),
        ));
    }

    public function test_underpaid_session_is_rejected(): void
    {
        $this->assertFalse($this->service()->sessionMatchesPayment(
            ['client_reference_id' => 'PAY-1-ABC', 'total_amount' => 1000],
            $this->payment(),
        ));
    }
}
