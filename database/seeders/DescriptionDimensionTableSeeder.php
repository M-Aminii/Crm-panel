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

        $DescriptionDimensions = [
            ['name' => 'سیلیکون IG','percent'=> null,'price' => 5300000],
            ['name' => 'الگویی 25','percent'=> 25,'price' => null],
            ['name' => 'اسپندرال','percent'=> null,'price' => 9100000],

        ];

        foreach ($DescriptionDimensions as $DescriptionDimension) {
            DescriptionDimension::create($DescriptionDimension);

        }
        $this->command->info('add DescriptionDimension to database');
    }
}
