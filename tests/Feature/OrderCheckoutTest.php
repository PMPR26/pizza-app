<?php

use App\Mail\OrderPlacedMail;
use App\Models\Order;
use App\Models\Pizza;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Mail::fake();
});

test('guest cannot checkout', function () {
    $response = $this->postJson('/api/orders/checkout', [
        'items' => [
            ['pizza_id' => 1, 'quantity' => 1],
        ],
    ]);

    $response->assertUnauthorized();
});

test('checkout creates orders with same checkout_group_id and sends one email with all lines', function () {
    $user = User::factory()->create([
        'role' => 'customer',
        'email' => 'buyer@example.com',
    ]);

    $p1 = Pizza::create([
        'name' => 'Criolla',
        'description' => 'Descripción de prueba con más de diez caracteres.',
        'price' => 25.00,
        'image' => 'https://example.com/a.jpg',
    ]);

    $p2 = Pizza::create([
        'name' => 'Pepperoni',
        'description' => 'Descripción de prueba con más de diez caracteres.',
        'price' => 30.00,
        'image' => 'https://example.com/b.jpg',
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/orders/checkout', [
        'items' => [
            ['pizza_id' => $p1->id, 'quantity' => 1],
            ['pizza_id' => $p2->id, 'quantity' => 2],
        ],
    ]);

    $response->assertOk()
        ->assertJsonPath('message', 'Pedido creado correctamente');

    expect(Order::count())->toBe(2);

    expect(Order::pluck('checkout_group_id')->unique()->count())->toBe(1);

    Mail::assertSent(OrderPlacedMail::class, 1);
    Mail::assertSent(OrderPlacedMail::class, function (OrderPlacedMail $mail) {
        return $mail->orders->count() === 2;
    });
});
