<?php

namespace Database\Seeders;

use App\Models\DescriptionDimension;
use App\Models\Product;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DescriptionDimensionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DescriptionDimension::count()) {
            DescriptionDimension::truncate();
        }

        $products = [
           'سیلیکون IG',
           'الگویی 25',
           'اسپندرال',
        ];

        foreach ($products as $product) {
            DescriptionDimension::create(['name' => $product]);

        }
        $this->command->info('add Product to database');
    }
}
