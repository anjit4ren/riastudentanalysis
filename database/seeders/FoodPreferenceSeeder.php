<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FoodPreference;

class FoodPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       FoodPreference::create(['name' => 'Vegetarian', 'active_status' => true]);
        FoodPreference::create(['name' => 'Non-Vegetarian', 'active_status' => true]);
        FoodPreference::create(['name' => 'Vegan', 'active_status' => false]);
    }
}
