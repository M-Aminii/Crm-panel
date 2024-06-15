<?php

namespace Database\Seeders;

use App\Models\GlassMaterial;
use App\Models\GlassSpacer;
use App\Models\GlassType;
use App\Models\Product;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlassSpacerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (GlassSpacer::count()) {
            GlassSpacer::truncate();
        }

        $GlassSpacers = [
            ['size' => 10, 'price' => 0],
            ['size' => 12, 'price' => 0],
            ['size' => 14, 'price' => 300000],
            ['size' => 16, 'price' => 600000],
            ['size' => 18, 'price' => 800000],
            ['size' => 20, 'price' => 1100000],
            ['size' => 22, 'price' => 1400000],
            ['size' => 24, 'price' => 1700000],
            ['size' => 26, 'price' => 2000000],
            ['size' => 28, 'price' => 2300000],

        ];

        foreach ($GlassSpacers as $GlassSpacer) {
            GlassSpacer::create($GlassSpacer);
        }
        $this->command->info('GlassSpacer entries added to the database');
    }
}
