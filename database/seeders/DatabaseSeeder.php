<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\CustomersSeeder;
use Database\Seeders\DelegateTypeSeeder;
use Database\Seeders\FoodPreferenceSeeder;
use Database\Seeders\ResidenceTypeSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        // $this->call(DelegateTypeSeeder::class);
        // $this->call([ FoodPreferenceSeeder::class ]);
        // $this->call([ ResidenceTypeSeeder::class ]);
        $this->call([ PortalStatusSeeder::class ]);


    }
}
