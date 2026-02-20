<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\User;
use App\Models\Department;
use App\Models\Category;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $departments = Department::all();
        $categories = Category::all();

        // 30 documents, dummy file metadata (nanti upload real kita buat)
        for ($i = 1; $i <= 30; $i++) {
            $dept = $departments->random();
            $cat = $categories->random();
            $uploader = $users->random();

            // ratio lebih kurang: 50% public, 30% department, 20% private
            $r = rand(1, 100);
            $access = $r <= 50 ? 'public' : ($r <= 80 ? 'department' : 'private');

            Document::create([
                'title' => "Document $i - {$cat->title}",
                'description' => "Sample description for document $i",
                'file_name' => "dummy_$i.pdf",
                'file_path' => "documents/dummy_$i.pdf",
                'file_type' => "pdf",
                'file_size' => rand(50_000, 2_000_000),
                'category_id' => $cat->id,
                'department_id' => $dept->id,
                'uploaded_by' => $uploader->id,
                'access_level' => $access,
                'download_count' => rand(0, 25),
            ]);
        }
    }
}
