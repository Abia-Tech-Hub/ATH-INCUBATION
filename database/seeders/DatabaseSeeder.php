<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $email = strtolower(trim((string) env('OWNER_EMAIL', '')));
        $password = (string) env('OWNER_PASSWORD', '');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->command?->warn('OWNER_EMAIL is not set to a valid email; skipping the owner account.');

            return;
        }
        if (strlen($password) < 12) {
            $this->command?->warn('OWNER_PASSWORD must be at least 12 characters; skipping the owner account.');

            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            ['role' => 'OWNER', 'password' => Hash::make($password)],
        );

        $this->command?->info("Owner ready: {$email}. Sign in at /admin/login and set up two-factor sign-in.");
    }
}
