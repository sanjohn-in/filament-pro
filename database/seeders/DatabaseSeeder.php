<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Admin\Theme::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Royal Gold & Ruby Silk (រាជរដ្ឋសិរីមង្គល)',
                'description' => 'Traditional Khmer Royal Red & Gold Wedding Theme with Kbach motifs and Moul calligraphy.',
                'price' => 0,
                'is_free' => true,
                'is_active' => true,
                'display_order' => 1,
            ]
        );

        \App\Models\Admin\Theme::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Heritage Lotus & Ivory Silk (កេរដំណែលផ្កាឈូកអង្គរ)',
                'description' => 'Sacred Lotus emblem theme with ivory silk tones and gentle floral elegance.',
                'price' => 0,
                'is_free' => true,
                'is_active' => true,
                'display_order' => 2,
            ]
        );

        \App\Models\Admin\Theme::updateOrCreate(
            ['id' => 3],
            [
                'name' => 'Modern Luxury Khmer Gold Fusion (ខ្មែរបុរាណទាន់សម័យ)',
                'description' => 'Obsidian and champagne gold fusion theme with modern geometry and authentic Khmer typography.',
                'price' => 0,
                'is_free' => true,
                'is_active' => true,
                'display_order' => 3,
            ]
        );
    }
}
