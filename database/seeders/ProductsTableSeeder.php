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
            ['name' => 'سکوریت سفارشی', 'image_path' => 'path_to_image1.jpg'],
            ['name' => 'لمینت سفارشی', 'image_path' => 'path_to_image2.jpg'],
            ['name' => 'دوجداره سفارشی', 'image_path' => 'path_to_image3.jpg'],
            ['name' => 'سه جداره سفارشی', 'image_path' => 'path_to_image4.jpg'],
            ['name' => 'چهار جداره سفارشی', 'image_path' => 'path_to_image5.jpg'],
            ['name' => 'دوجداره لمینت سفارشی', 'image_path' => 'path_to_image6.jpg'],
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
                    ['name' => 'دو لایه', 'image_path' => 'image/product_images/laminet_2.jpg'],
                    ['name' => 'سه لایه', 'image_path' => 'path_to_section_image2.jpg'],
                    ['name' => 'چهار لایه', 'image_path' => 'path_to_section_image3.jpg'],
                    ['name' => 'پنج لایه', 'image_path' => 'path_to_section_image2.jpg'],
                    ['name' => 'شش لایه', 'image_path' => 'path_to_section_image3.jpg'],
                    ['name' => 'هفت لایه', 'image_path' => 'path_to_section_image3.jpg'],
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
                    ['name' => 'یک لایه لمینت', 'image_path' => 'path_to_section_image4.jpg'],
                    ['name' => 'دو لایه لمینت', 'image_path' => 'path_to_section_image5.jpg'],
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
