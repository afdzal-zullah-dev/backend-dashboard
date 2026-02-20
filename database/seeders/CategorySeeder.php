<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['title' => 'Policy',   'description' => 'Company policies and procedures'],
            ['title' => 'Report',   'description' => 'Monthly/quarterly reports'],
            ['title' => 'Template', 'description' => 'Reusable templates'],
            ['title' => 'Guide',    'description' => 'How-to guides and manuals'],
            ['title' => 'Form',     'description' => 'Forms for internal use'],
            ['title' => 'Other',    'description' => 'Miscellaneous documents'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
