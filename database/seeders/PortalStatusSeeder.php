<?php
namespace Database\Seeders;

use App\Models\PortalStatus;
use Illuminate\Database\Seeder;

class PortalStatusSeeder extends Seeder
{
    public function run(): void
    {
        PortalStatus::insert([
            [
                'status_name' => 'Open',
                'message' => 'Registration is now open.',
                'active_status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'Closed',
                'message' => 'Registration is currently closed.',
                'active_status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'Maintenance',
                'message' => 'Portal is under maintenance.',
                'active_status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
