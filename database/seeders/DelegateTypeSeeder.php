<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DelegateType;

class DelegateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DelegateType::insert([
        ['name' => 'independent', 'active_status' => true, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'institutional', 'active_status' => true, 'created_at' => now(), 'updated_at' => now()],
    ]);
    }
}
