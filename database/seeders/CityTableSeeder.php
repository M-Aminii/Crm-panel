<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
        public function run()
    {
        $file = public_path('fileCities/cities.csv'); // مسیر فایل CSV یا اکسل

        // خواندن فایل CSV و وارد کردن اطلاعات به دیتابیس
        $data = array_map('str_getcsv', file($file));

       foreach ($data as $row) {

           DB::table('cities')->insert([
                'name' =>$row[1],
                'province_id' => $row[2],
            ]);
           //$this->command->info('add city to database');
        }
    }
}
