<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $incomeCategories = [
            ['name' => 'Gaji', 'icon' => 'briefcase', 'color' => '#0F766E'],
            ['name' => 'Freelance', 'icon' => 'laptop', 'color' => '#14B8A6'],
            ['name' => 'Investasi', 'icon' => 'trending-up', 'color' => '#059669'],
            ['name' => 'Hadiah', 'icon' => 'gift', 'color' => '#6366F1'],
            ['name' => 'Lainnya', 'icon' => 'plus-circle', 'color' => '#8B5CF6'],
        ];

        $expenseCategories = [
            ['name' => 'Makanan', 'icon' => 'utensils', 'color' => '#EF4444'],
            ['name' => 'Transportasi', 'icon' => 'car', 'color' => '#F97316'],
            ['name' => 'Belanja', 'icon' => 'shopping-bag', 'color' => '#EC4899'],
            ['name' => 'Tagihan', 'icon' => 'file-text', 'color' => '#F59E0B'],
            ['name' => 'Hiburan', 'icon' => 'film', 'color' => '#8B5CF6'],
            ['name' => 'Kesehatan', 'icon' => 'heart', 'color' => '#10B981'],
            ['name' => 'Pendidikan', 'icon' => 'book-open', 'color' => '#3B82F6'],
            ['name' => 'Lainnya', 'icon' => 'more-horizontal', 'color' => '#6B7280'],
        ];

        foreach ($incomeCategories as $category) {
            Category::create([
                'user_id' => null,
                'name' => $category['name'],
                'type' => 'income',
                'icon' => $category['icon'],
                'color' => $category['color'],
                'is_default' => true,
            ]);
        }

        foreach ($expenseCategories as $category) {
            Category::create([
                'user_id' => null,
                'name' => $category['name'],
                'type' => 'expense',
                'icon' => $category['icon'],
                'color' => $category['color'],
                'is_default' => true,
            ]);
        }
    }
}
