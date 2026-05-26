<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = ['Electronics', 'Clothing', 'Groceries', 'Furniture', 'Books'];
        foreach ($categories as $cat) {
            Category::create(['name' => $cat]);
        }
    }
}
