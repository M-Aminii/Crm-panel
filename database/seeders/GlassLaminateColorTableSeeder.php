<?php

namespace Database\Seeders;

use App\Models\GlassLaminate;
use App\Models\GlassLaminateColor;
use App\Models\GlassMaterial;
use App\Models\GlassSpacer;
use App\Models\GlassType;
use App\Models\Product;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlassLaminateColorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (GlassLaminateColor::count()) {
            GlassLaminateColor::truncate();
        }

        $GlassLaminateColors = [
            ['english_name' => 'normal', 'price' => 0],
            ['english_name' => 'hued', 'price' => 4400000],
        ];

        foreach ($GlassLaminateColors as $GlassLaminateColor) {
            GlassLaminateColor::create($GlassLaminateColor);
        }
        $this->command->info('GlassLaminateColor entries added to the database');
    }
}
