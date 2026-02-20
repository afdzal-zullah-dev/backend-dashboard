<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\Api\V1\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = Category::select('id', 'title', 'description')
            ->orderBy('title')
            ->get();

        return CategoryResource::collection($categories);
    }
}
