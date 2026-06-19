<?php

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('canBeCancelled retorna true para status pending', function () {
    $order = Order::factory()->create([
        'status' => Order::STATUS_PENDING,
    ]);

    expect($order->canBeCancelled())->toBeTrue();
});

it('canBeCancelled retorna true para status processing', function () {
    $order = Order::factory()->create([
        'status' => Order::STATUS_PROCESSING,
    ]);

    expect($order->canBeCancelled())->toBeTrue();
});

it('canBeCancelled retorna false para status shipped', function () {
    $order = Order::factory()->create([
        'status' => Order::STATUS_SHIPPED,
    ]);

    expect($order->canBeCancelled())->toBeFalse();
});

it('canBeCancelled retorna false para status delivered', function () {
    $order = Order::factory()->create([
        'status' => Order::STATUS_DELIVERED,
    ]);

    expect($order->canBeCancelled())->toBeFalse();
});

it('canBeCancelled retorna false para status cancelled', function () {
    $order = Order::factory()->create([
        'status' => Order::STATUS_CANCELLED,
    ]);

    expect($order->canBeCancelled())->toBeFalse();
});

it('markAsShipped muda status para shipped e salva tracking_number', function () {
    $order = Order::factory()->create([
        'status' => Order::STATUS_PENDING,
        'tracking_number' => null,
    ]);

    $order->markAsShipped('BR123456789');

    $order->refresh();

    expect($order->status)->toBe(Order::STATUS_SHIPPED)
        ->and($order->tracking_number)->toBe('BR123456789');
});

it('markAsShipped nao altera se ja esta shipped', function () {
    $order = Order::factory()->create([
        'status' => Order::STATUS_SHIPPED,
        'tracking_number' => 'BR123456789',
    ]);

    $order->markAsShipped('BR987654321');

    $order->refresh();

    expect($order->status)->toBe(Order::STATUS_SHIPPED)
        ->and($order->tracking_number)->toBe('BR123456789');
});

it('getStatus retorna o status capitalizado', function () {
    $order = Order::factory()->create([
        'status' => Order::STATUS_PENDING,
    ]);

    expect($order->getStatus())->toBe('Pending');
});
