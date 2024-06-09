<?php

namespace Database\Seeders;

use App\Models\GlassMaterial;
use App\Models\GlassType;
use App\Models\Product;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlassMaterialTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (GlassMaterial::count()) {
            GlassMaterial::truncate();
        }

        $GlassMaterials = [
            ['name' => 'خام', 'price' => 0],
            ['name' => 'سکوریت', 'price' => 1500000],
        ];

        foreach ($GlassMaterials as $GlassMaterial) {
            GlassMaterial::create( $GlassMaterial);

        }
        $this->command->info('add GlassMaterial to database');
    }
}
