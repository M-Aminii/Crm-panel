<?php

namespace Database\Seeders;

use App\Models\GlassType;
use App\Models\GlassWidth;
use App\Models\Product;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlassWidthTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (GlassWidth::count()) {
            GlassWidth::truncate();
        }

        $GlassTypes = [
           '3',
           '4',
           '5',
           '6',
           '8',
           '10',
           '12',
        ];

        foreach ($GlassTypes as $GlassType) {
            GlassWidth::create(['size' => $GlassType]);

        }
        $this->command->info('add GlassWidth to database');
    }
}
