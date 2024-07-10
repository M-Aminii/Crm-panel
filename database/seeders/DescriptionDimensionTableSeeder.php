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
            ['name' => 'سیلیکون SG','percent'=> null,'price' => 7200000],
            ['name' => 'اسپندرال','percent'=> null,'price' => 9100000],
            ['name' => '1 سوراخ','percent'=> null,'price' => 100000],
            ['name' => '1 سوراخ ضریب 2','percent'=> null,'price' => 200000],
            ['name' => '2 سوراخ','percent'=> null,'price' => 200000],
            ['name' => '2 سوراخ ضریب 2','percent'=> null,'price' => 400000],
            ['name' => '3 سوراخ','percent'=> null,'price' => 300000],
            ['name' => '3 سوراخ ضریب 2','percent'=> null,'price' => 600000],
            ['name' => '4 سوراخ','percent'=> null,'price' => 400000],
            ['name' => '4 سوراخ ضریب 2','percent'=> null,'price' => 800000],
            ['name' => '5 سوراخ','percent'=> null,'price' => 500000],
            ['name' => '5 سوراخ ضریب 2','percent'=> null,'price' => 1000000],
            ['name' => '6 سوراخ','percent'=> null,'price' => 600000],
            ['name' => '6 سوراخ ضریب 2','percent'=> null,'price' => 1200000],
            ['name' => '7 سوراخ','percent'=> null,'price' => 700000],
            ['name' => '7 سوراخ ضریب 2','percent'=> null,'price' => 1400000],
            ['name' => '8 سوراخ','percent'=> null,'price' => 800000],
            ['name' => '8 سوراخ ضریب 2','percent'=> null,'price' => 1600000],
            ['name' => '9 سوراخ','percent'=> null,'price' => 900000],
            ['name' => '9 سوراخ ضریب 2','percent'=> null,'price' => 1800000],
            ['name' => '10 سوراخ','percent'=> null,'price' => 1000000],
            ['name' => '10 سوراخ ضریب 2','percent'=> null,'price' => 2000000],
            ['name' => '11 سوراخ','percent'=> null,'price' => 1100000],
            ['name' => '11 سوراخ ضریب 2','percent'=> null,'price' => 2200000],
            ['name' => '12 سوراخ','percent'=> null,'price' => 1200000],
            ['name' => '12 سوراخ ضریب 2','percent'=> null,'price' => 2400000],
            ['name' => '13 سوراخ','percent'=> null,'price' => 1300000],
            ['name' => '13 سوراخ ضریب 2','percent'=> null,'price' => 2600000],
            ['name' => '14 سوراخ','percent'=> null,'price' => 1400000],
            ['name' => '14 سوراخ ضریب 2','percent'=> null,'price' => 2800000],
            ['name' => '15 سوراخ','percent'=> null,'price' => 1500000],
            ['name' => '15 سوراخ ضریب 2','percent'=> null,'price' => 3000000],
        ];

        foreach ($DescriptionDimensions as $DescriptionDimension) {
            DescriptionDimension::create($DescriptionDimension);

        }
        $this->command->info('add DescriptionDimension to database');
    }
}
