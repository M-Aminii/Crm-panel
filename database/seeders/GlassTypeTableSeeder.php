<?php

namespace Database\Seeders;

use App\Models\GlassType;
use App\Models\Product;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlassTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (GlassType::count()) {
            GlassType::truncate();
        }

        $GlassTypes = [
            ['name' => 'سوپر کلیر', 'price' => 0],
            ['name' => 'دودی', 'price' => 650000],
            ['name' => 'برنز', 'price' => 400000],
            ['name' => 'رفلکس طلایی', 'price' => 350000],
            ['name' => 'رفلکس نقره ای', 'price' => 350000],
            ['name' => 'ساتینا', 'price' => 1300000],
            ['name' => 'ساتینا(زبرا)', 'price' => 2450000],
        ];

        foreach ($GlassTypes as $GlassType) {
            GlassType::create($GlassType);

        }
        $this->command->info('add GlassTypes to database');
    }
}
