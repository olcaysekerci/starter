<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\User\Models\User;
use App\Modules\User\Models\Role;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:super-admin {email} {password} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a super admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->argument('name') ?? 'Super Admin';

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }

        // Create user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'email_verified_at' => now(),
        ]);

        // Assign super-admin role
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $user->assignRole($superAdminRole);
            $this->info("Super admin user created successfully!");
            $this->info("Email: {$email}");
            $this->info("Password: {$password}");
        } else {
            $this->error("Super admin role not found! Please run the seeder first.");
            return 1;
        }

        return 0;
    }
}
