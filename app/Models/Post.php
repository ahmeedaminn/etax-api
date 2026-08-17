<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // 1. Mass Assignment Protection
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'content'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // 2. Who wrote this post?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 3. Which folder/category does this post live in?
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 4. THE POLYMORPHIC MAGIC: What files are attached to this post?
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}