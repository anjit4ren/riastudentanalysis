<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Committee;

class CommitteeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Committee::insert([
            ['name' => 'Organizing Committee',  'short_name' => 'OC', 'active_status' => true],
            ['name' => 'Technical Committee', 'short_name' => 'TC', 'active_status' => true],
            ['name' => 'Logistics Committee', 'short_name' => 'LC', 'active_status' => false],
        ]);
    }
}
