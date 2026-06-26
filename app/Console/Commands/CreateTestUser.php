<?php

namespace App\Console\Commands;

use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('app:create-test-user')]
#[Description('Create test user with team')]
class CreateTestUser extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = 'kaiquewlb@gmail.com';
        
        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error('User already exists!');
            return 1;
        }

        // Create user
        $user = User::create([
            'name' => 'Kaique',
            'email' => $email,
            'password' => Hash::make('Kaique12345-'),
            'email_verified_at' => now(),
        ]);

        // Create team
        $team = Team::create([
            'name' => 'Kaique Team',
            'slug' => 'kaique-team',
            'user_id' => $user->id,
        ]);

        // Set current team
        $user->update(['current_team_id' => $team->id]);

        // Create membership
        Membership::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        $this->info('User created successfully!');
        $this->info("Email: {$email}");
        $this->info('Password: Kaique12345-');
        
        return 0;
    }
}
