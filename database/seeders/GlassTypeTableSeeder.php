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
           'سوپر کلیر',
           'دودی',
           'برنز',
           'رفلکس نقره ای',
           'رفلکس طلایی',
           'ساتینا',
           'آینه',
           'سانرژی',
        ];

        foreach ($GlassTypes as $GlassType) {
            GlassType::create(['name' => $GlassType]);

        }
        $this->command->info('add GlassTypes to database');
    }
}
