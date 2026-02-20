<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DocumentController;
use App\Models\Document;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Auth routes contoh
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'me']);

        // Master data
        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::get('/categories', [CategoryController::class, 'index']);

        // DOCUMENTS

        // INDEX – tak guna policy, sebab dah filter ikut role dalam controller
        Route::get('/documents', [DocumentController::class, 'index']);

        // SHOW – guna policy view
        Route::get('/documents/{document}', [DocumentController::class, 'show'])
            ->middleware('can:view,document');

        // STORE – guna policy create
        Route::post('/documents', [DocumentController::class, 'store'])
            ->middleware('can:create,' . Document::class);

        // UPDATE – guna policy update
        Route::patch('/documents/{document}', [DocumentController::class, 'update'])
            ->middleware('can:update,document');

        // DELETE – guna policy delete
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])
            ->middleware('can:delete,document');

        // DOWNLOAD – guna policy download
        Route::get('/documents/{document}/download', [DocumentController::class, 'download'])
            ->middleware('can:download,document');
    });
});