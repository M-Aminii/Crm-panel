<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\GlassWidth;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ProvincesTableSeeder::class);
        $this->call(CityTableSeeder::class);
        $this->call(ProductTableSeeder::class);
        $this->call(GlassTypeTableSeeder::class);
        $this->call(GlassWidthTableSeeder::class);
        $this->call(GlassMaterialTableSeeder::class);
        $this->call(GlassSpacerTableSeeder::class);
        $this->call(GlassSpacerColorTableSeeder::class);
        $this->call(GlassSpacerGlueTableSeeder::class);
        $this->call(GlassLaminateTableSeeder::class);
        $this->call(GlassLaminateColorTableSeeder::class);
        $this->call(DescriptionDimensionTableSeeder::class);
    }
}
