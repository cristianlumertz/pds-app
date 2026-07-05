<?php

namespace Tests\Unit;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_be_cancelled_returns_true_for_pending_status(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
        ]);

        $this->assertTrue($order->canBeCancelled());
    }

    public function test_can_be_cancelled_returns_true_for_processing_status(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PROCESSING,
        ]);

        $this->assertTrue($order->canBeCancelled());
    }

    public function test_can_be_cancelled_returns_false_for_shipped_status(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_SHIPPED,
        ]);

        $this->assertFalse($order->canBeCancelled());
    }

    public function test_can_be_cancelled_returns_false_for_delivered_status(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_DELIVERED,
        ]);

        $this->assertFalse($order->canBeCancelled());
    }

    public function test_can_be_cancelled_returns_false_for_cancelled_status(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_CANCELLED,
        ]);

        $this->assertFalse($order->canBeCancelled());
    }

    public function test_is_paid_returns_true_only_for_paid_payment_status(): void
    {
        $paidOrder = Order::factory()->create([
            'payment_status' => Order::PAYMENT_STATUS_PAID,
        ]);

        $pendingOrder = Order::factory()->create([
            'payment_status' => Order::PAYMENT_STATUS_PENDING,
        ]);

        $this->assertTrue($paidOrder->isPaid());
        $this->assertFalse($pendingOrder->isPaid());
    }

    public function test_mark_as_shipped_changes_status_and_saves_tracking_number(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'tracking_number' => null,
        ]);

        $order->markAsShipped('BR123456789');
        $order->refresh();

        $this->assertSame(Order::STATUS_SHIPPED, $order->status);
        $this->assertSame('BR123456789', $order->tracking_number);
    }

    public function test_mark_as_shipped_does_not_change_existing_shipped_order(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_SHIPPED,
            'tracking_number' => 'BR123456789',
        ]);

        $order->markAsShipped('BR987654321');
        $order->refresh();

        $this->assertSame(Order::STATUS_SHIPPED, $order->status);
        $this->assertSame('BR123456789', $order->tracking_number);
    }

    public function test_get_status_returns_capitalized_status(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
        ]);

        $this->assertSame('Pending', $order->getStatus());
    }
}
