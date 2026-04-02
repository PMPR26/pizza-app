<?php

use App\Models\Pizza;

test('pizzas index is public and returns paginated data', function () {
    Pizza::create([
        'name' => 'Muzzarella',
        'description' => 'Descripción de prueba con más de diez caracteres.',
        'price' => 20.00,
        'image' => 'https://example.com/p.jpg',
    ]);

    $response = $this->getJson('/api/pizzas');

    $response->assertOk()
        ->assertJsonStructure([
            'data',
            'current_page',
            'per_page',
            'total',
        ]);

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.name'))->toBe('Muzzarella');
});
