<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;

class DepartmentController extends Controller
{
    /**
     * GET /api/v1/departments
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Departments fetched',
            'data' => Department::orderBy('name')->get(),
        ], 200);
    }
}
