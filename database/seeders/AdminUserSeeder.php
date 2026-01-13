<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserSetting;
use App\Models\Monitor;
use App\Models\PingLog;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        // Create user settings for admin
        UserSetting::create([
            'user_id' => $admin->id,
            'dark_mode' => true,
            'notification_settings' => [
                'email' => true,
                'web' => true,
                'down_alert' => true,
                'up_alert' => true,
            ],
        ]);

        // Create regular user
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('user123'),
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);

        // Create user settings for regular user
        UserSetting::create([
            'user_id' => $user->id,
            'dark_mode' => true,
            'notification_settings' => [
                'email' => false,
                'web' => true,
                'down_alert' => true,
                'up_alert' => false,
            ],
        ]);

        // Create demo monitors
        $monitors = [
            [
                'name' => 'Google',
                'url' => 'https://www.google.com',
                'type' => 'http',
                'interval' => 3,
                'is_active' => true,
                'last_status' => true,
                'uptime_percentage' => 99.8,
            ],
            [
                'name' => 'GitHub',
                'url' => 'https://github.com',
                'type' => 'http',
                'interval' => 5,
                'is_active' => true,
                'last_status' => true,
                'uptime_percentage' => 99.5,
            ],
            [
                'name' => 'Localhost',
                'url' => 'http://localhost',
                'type' => 'http',
                'interval' => 10,
                'is_active' => false,
                'last_status' => false,
                'uptime_percentage' => 0,
            ],
            [
                'name' => 'Cloudflare DNS',
                'url' => 'https://1.1.1.1',
                'type' => 'http',
                'interval' => 3,
                'is_active' => true,
                'last_status' => true,
                'uptime_percentage' => 99.9,
            ],
            [
                'name' => 'Example.com',
                'url' => 'https://example.com',
                'type' => 'http',
                'interval' => 5,
                'is_active' => true,
                'last_status' => true,
                'uptime_percentage' => 98.7,
            ],
        ];

        foreach ($monitors as $monitorData) {
            $monitor = Monitor::create($monitorData);
            
            // Create some ping logs for demo
            for ($i = 0; $i < 50; $i++) {
                $status = rand(0, 10) > 1; // 90% uptime
                $responseTime = $status ? rand(50, 300) : null;
                
                PingLog::create([
                    'monitor_id' => $monitor->id,
                    'status' => $status,
                    'response_time' => $responseTime,
                    'status_code' => $status ? 200 : 500,
                    'created_at' => now()->subMinutes(rand(0, 1440)), // Last 24 hours
                ]);
            }
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('📋 Login Credentials:');
        $this->command->info('   👑 Admin: admin@example.com / admin123');
        $this->command->info('   👤 User: user@example.com / user123');
        $this->command->info('   📊 Created 5 demo monitors with ping logs');
    }
}