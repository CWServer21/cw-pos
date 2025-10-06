<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(Database\Seeders\Tenant\TenantDatabaseSeeder::class);

        if (Schema::hasTable('general_settings')) {
            $general_setting = DB::table('general_settings')->select('modules')->first();


            if (in_array('restaurant', explode(',', $general_setting->modules))) {
                $this->call(\Modules\Restaurant\Database\Seeders\RestaurantDatabaseSeeder::class);
            }
        }
    }
}
