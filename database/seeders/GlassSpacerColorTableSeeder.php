<?php

namespace Database\Seeders;

use App\Models\GlassMaterial;
use App\Models\GlassSpacer;
use App\Models\GlassSpacerColor;
use App\Models\GlassType;
use App\Models\Product;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlassSpacerColorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (GlassSpacerColor::count()) {
            GlassSpacerColor::truncate();
        }

        $GlassSpacers = [
            ['name' => 'نقره ای', 'price' => 0],
            ['name' => 'مشکی', 'price' => 150000],
        ];

        foreach ($GlassSpacers as $GlassSpacer) {
            GlassSpacerColor::create($GlassSpacer);
        }
        $this->command->info('GlassSpacerColor entries added to the database');
    }
}
