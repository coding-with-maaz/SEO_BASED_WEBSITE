<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists
        $adminEmail = env('ADMIN_EMAIL', 'admin@nazaarabox.com');
        
        $admin = User::where('email', $adminEmail)->first();

        if (!$admin) {
            User::create([
                'name' => env('ADMIN_NAME', 'Administrator'),
                'email' => $adminEmail,
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);

            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: ' . $adminEmail);
            $this->command->warn('Password: ' . env('ADMIN_PASSWORD', 'admin123'));
            $this->command->warn('Please change the default password after first login!');
        } else {
            // Update existing user to be admin
            $admin->update([
                'is_admin' => true,
            ]);
            $this->command->info('Existing user updated to admin: ' . $adminEmail);
        }
    }
}
