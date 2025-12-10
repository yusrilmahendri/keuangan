<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        category::insert([
            ['name' => 'income'],
            ['name' => 'outcome'],
            ['name' => 'expense'],
            ['name' => 'savings'],
        ]);
    }
}
