<?php

use App\Enums\UserRole;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('admin can open the overview dashboard', function () {
    $admin = User::factory()->create([
        'role' => UserRole::ADMIN,
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Dashboard')
        ->has('stats')
        ->has('salesAnalytics')
        ->has('topProducts')
        ->has('leastProducts')
        ->has('revenueByDay')
        ->has('recentServices')
        ->has('recent_orders')
        ->has('recent_invoices'),
    );
});
