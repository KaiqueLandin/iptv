<?php

use App\Models\Product;
use App\Models\User;

test('guests are redirected to register and returned to checkout after buying', function () {
    $product = Product::create([
        'name' => 'Plano 25 Créditos',
        'slug' => 'plano-25-creditos',
        'price' => 99.90,
        'is_active' => true,
    ]);

    $response = $this->get(route('checkout.buy', $product));

    $response->assertRedirect(route('register'));
    expect(session('url.intended'))->toBe(route('checkout.buy', $product));
});

test('inactive products cannot be purchased', function () {
    $product = Product::create([
        'name' => 'Plano Inativo',
        'slug' => 'plano-inativo',
        'price' => 50,
        'is_active' => false,
    ]);

    $this->get(route('checkout.buy', $product))->assertNotFound();
});

test('authenticated users go straight to checkout when buying', function () {
    $user = User::factory()->create();
    $product = Product::create([
        'name' => 'Plano 50 Créditos',
        'slug' => 'plano-50-creditos',
        'price' => 149.90,
        'is_active' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('checkout.buy', $product));

    $invoice = $user->invoices()->latest()->first();

    expect($invoice)->not->toBeNull();
    $response->assertRedirect(route('checkout.show', $invoice));
});
