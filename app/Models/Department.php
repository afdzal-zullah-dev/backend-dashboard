<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Document;
use App\Models\User;

class Department extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Department has many documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Department has many users
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
