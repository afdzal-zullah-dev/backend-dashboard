<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Document;

class Category extends Model
{
    protected $fillable = [
        'title',
        'description',
    ];

    /**
     * Category has many documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
