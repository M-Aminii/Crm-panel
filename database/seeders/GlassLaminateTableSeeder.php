<?php

namespace Database\Seeders;

use App\Models\GlassLaminate;
use App\Models\GlassMaterial;
use App\Models\GlassSpacer;
use App\Models\GlassType;
use App\Models\Product;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlassLaminateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (GlassLaminate::count()) {
            GlassLaminate::truncate();
        }

        $GlassLaminates = [
            ['size' => '0/38', 'price' => 8400000],
            ['size' => '0/76', 'price' => 10900000],
            ['size' => '1/52', 'price' => 16100000],
        ];

        foreach ($GlassLaminates as $GlassLaminate) {
            GlassLaminate::create($GlassLaminate);
        }
        $this->command->info('GlassLaminate entries added to the database');
    }
}
