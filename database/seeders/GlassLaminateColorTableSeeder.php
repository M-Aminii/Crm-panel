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
            ['name' => 'شفاف', 'price' => 0],
            ['name' => 'شیری', 'price' => 4400000],
            ['name' => 'سفید', 'price' => 4400000],
            ['name' => 'مشکی', 'price' => 4400000],
        ];

        foreach ($GlassLaminateColors as $GlassLaminateColor) {
            GlassLaminateColor::create($GlassLaminateColor);
        }
        $this->command->info('GlassLaminateColor entries added to the database');
    }
}
