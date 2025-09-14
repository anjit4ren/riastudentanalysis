<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ResidenceType;

class ResidenceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ResidenceType::insert([
            ['name' => 'non residence', 'active_status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'residence', 'active_status' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
