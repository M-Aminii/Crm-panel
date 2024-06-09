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

        $GlassWidths = [
            ['size' => 3, 'price' => 0],
            ['size' =>  3.5, 'price' => 0], ///TODO: عدد به صورت کامل درج میشه نه به صورت اعشار
            ['size' => 4, 'price' => 0],
            ['size' => 5, 'price' => 0],
            ['size' => 6, 'price' => 900000],
            ['size' => 8, 'price' => 2800000],
            ['size' => 10, 'price' => 4900000],
            ['size' => 12, 'price' => 6700000],
            ['size' => 15, 'price' => 14100000],
        ];

        foreach ($GlassWidths as $GlassWidth) {
            GlassWidth::create($GlassWidth);

        }
        $this->command->info('add GlassWidth to database');
    }
}
