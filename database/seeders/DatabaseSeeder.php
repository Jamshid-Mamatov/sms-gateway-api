<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Project;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Providers ──────────────────────────────────────────────────────
        $fake = Provider::create([
            'name'      => 'Fake Driver',
            'driver'    => 'fake',
            'config'    => null,
            'is_active' => true,
        ]);

        $eskiz = Provider::create([
            'name'   => 'Eskiz.uz',
            'driver' => 'eskiz',
            'config' => [
                'login'    => 'your-eskiz-email@example.com',
                'password' => 'your-eskiz-password',
                'from'     => '4546',
            ],
            'is_active' => true,
        ]);

        $playmobile = Provider::create([
            'name'   => 'Playmobile',
            'driver' => 'playmobile',
            'config' => [
                'login'    => 'your-playmobile-login',
                'password' => 'your-playmobile-password',
                'from'     => 'INFO',
            ],
            'is_active' => true,
        ]);

        // ── Demo Projects ──────────────────────────────────────────────────
        $ecommerce = Project::create([
            'name'        => 'E-Commerce',
            'description' => 'Order confirmation and delivery notifications',
            'provider_id' => $fake->id,
        ]);

        $hr = Project::create([
            'name'        => 'HR System',
            'description' => 'Employee alerts and OTP codes',
            'provider_id' => $fake->id,
        ]);

        $this->command->info('');
        $this->command->info('✅  Seeding complete!');
        $this->command->table(
            ['Project', 'API Key'],
            [
                [$ecommerce->name, $ecommerce->api_key],
                [$hr->name,        $hr->api_key],
            ]
        );
        $this->command->info('');
        $this->command->warn('⚠   Save these API keys — they won\'t be shown again!');
    }
}
