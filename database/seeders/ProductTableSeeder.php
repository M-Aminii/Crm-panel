<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Product::count()) {
            Product::truncate();
        }

        $products = [
           'سکوریت سفارشی',
           'لمینت سفارشی',
           'دوجداره سفارشی',
           'سه جداره سفارشی',
           'چهار جداره سفارشی',
           'لمینت دوجداره سفارشی',
        ];

        foreach ($products as $product) {
            Product::create(['name' => $product]);

        }
        $this->command->info('add Product to database');
    }
}
