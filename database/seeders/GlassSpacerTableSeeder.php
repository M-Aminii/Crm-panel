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
           '1/52',
           '0/76',
           '0/38',
        ];

        foreach ($GlassSpacers as $GlassSpacer) {
            GlassSpacer::create(['size' => $GlassSpacer]);

        }
        $this->command->info('add GlassSpacer to database');
    }
}
