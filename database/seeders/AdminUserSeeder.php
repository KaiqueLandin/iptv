<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Ensure a working admin account exists.
     *
     * Idempotent: running it multiple times will not create duplicates.
     * It promotes a known account to admin and creates a fallback admin.
     */
    public function run(): void
    {
        // Promote the project owner's account to admin (if it exists).
        $owner = User::where('email', 'kaiquewlb@gmail.com')->first();

        if ($owner) {
            $owner->forceFill([
                'role' => UserRole::ADMIN,
                'is_active' => true,
                'email_verified_at' => $owner->email_verified_at ?? now(),
            ])->save();
        }

        // Fallback admin account so the panel is always reachable.
        // firstOrCreate ensures the password is only set on first creation —
        // subsequent runs will NOT overwrite a password changed in production.
        $fallback = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        // Always ensure role and active status are correct, without touching password.
        $fallback->forceFill([
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ])->save();
    }
}
