<?php

namespace Database\Seeders;

use App\Models\GlassMaterial;
use App\Models\GlassSpacer;
use App\Models\GlassSpacerColor;
use App\Models\GlassSpacerGlue;
use App\Models\GlassType;
use App\Models\Product;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlassSpacerGlueTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (GlassSpacerGlue::count()) {
            GlassSpacerGlue::truncate();
        }
        $GlassSpacers = [
            ['name' => 'پلی سولفاید', 'price' => 0],
            ['name' => 'سیلیکون IG', 'price' => 5300000],
            ['name' => 'سیلیکون SG', 'price' => 7200000],
        ];

        foreach ($GlassSpacers as $GlassSpacer) {
            GlassSpacerGlue::create($GlassSpacer);
        }
        $this->command->info('GlassSpacerGlue entries added to the database');
    }
}
