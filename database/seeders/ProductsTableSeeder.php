<?php
// database/seeders/ProductsTableSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductSection;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        // تعریف محصولات اصلی به همراه تصویر
        $products = [
            ['name' => 'سکوریت سفارشی', 'image_path' => 'image/product_images/scorit.png'],
            ['name' => 'لمینت سفارشی', 'image_path' => ''],
            ['name' => 'دوجداره سفارشی', 'image_path' => 'image/product_images/j-2.png'],
            ['name' => 'سه جداره سفارشی', 'image_path' => 'image/product_images/j-3.png'],
            ['name' => 'دوجداره لمینت سفارشی', 'image_path' => ''],
        ];

        // ایجاد محصولات اصلی در دیتابیس
        foreach ($products as $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'image_path' => $productData['image_path'],
            ]);

            // اگر محصولی دارای بخش‌هایی باشد، آنها را نیز اضافه کنید
            if ($product->name == 'لمینت سفارشی') {
                $sections = [
                    ['name' => 'دو لایه', 'image_path' => 'image/product_images/laminet_2.png'],
                    ['name' => 'سه لایه', 'image_path' => 'image/product_images/laminet_3.png'],
                    ['name' => 'چهار لایه', 'image_path' => 'image/product_images/laminet_4.png'],
                    ['name' => 'پنج لایه', 'image_path' => 'image/product_images/laminet_5.png'],
                    ['name' => 'شش لایه', 'image_path' => 'image/product_images/laminet_6.png'],
                    ['name' => 'هفت لایه', 'image_path' => 'image/product_images/laminet_7.png'],
                ];
                foreach ($sections as $sectionData) {
                    ProductSection::create([
                        'product_id' => $product->id,
                        'name' => $sectionData['name'],
                        'image_path' => $sectionData['image_path'],
                    ]);
                }
            } elseif ($product->name == 'دوجداره لمینت سفارشی') {
                $sections = [
                    ['name' => 'یک لایه لمینت', 'image_path' => 'image/product_images/laminet_doj.png'],
                    ['name' => 'دو لایه لمینت', 'image_path' => 'image/product_images/laminet_doj_2.png'],
                ];
                foreach ($sections as $sectionData) {
                    ProductSection::create([
                        'product_id' => $product->id,
                        'name' => $sectionData['name'],
                        'image_path' => $sectionData['image_path'],
                    ]);
                }
            }
        }
    }
}
