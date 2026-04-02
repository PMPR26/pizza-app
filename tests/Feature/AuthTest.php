<?php

use App\Models\User;

test('register returns token and creates customer', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Cliente Test',
        'email' => 'nuevo@example.com',
        'password' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonPath('message', 'Registro exitoso')
        ->assertJsonStructure(['data', 'token']);

    expect(User::where('email', 'nuevo@example.com')->exists())->toBeTrue();
});

test('login returns token for valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'login@example.com',
        'password' => 'password123',
        'role' => 'customer',
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'login@example.com',
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonPath('message', 'Login exitoso')
        ->assertJsonStructure(['data', 'token']);
});
