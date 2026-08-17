<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    // 1. Tell Laravel to always send our "fake" column to React
    protected $appends = ['image_url'];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function image()
    {
        return $this->morphOne(\App\Models\File::class, 'fileable')->latestOfMany();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (Magic Attributes)
    |--------------------------------------------------------------------------
    */

    // 2. Build the fake column by generating a full http://... link
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image->file_path);
        }
        
        return null;
    }
}