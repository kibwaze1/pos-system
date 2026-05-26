<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run()
    {
        $brands = ['Samsung', 'Nike', 'Apple', 'Sony', 'Adidas'];
        foreach ($brands as $brand) {
            Brand::create(['name' => $brand]);
        }
    }
}
