<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['file_path', 'fileable_id', 'fileable_type', 'user_id', 'file_name'];

    // 1. Tell Laravel to automatically append our custom 'url' attribute
    protected $appends = ['url'];

    // 2. The Accessor function (The Laravel version of a Virtual field!)
    public function getUrlAttribute()
    {
        // If you used the 'public' disk to save the file:
        return asset('storage/'.$this->file_path);

        // OR, if you saved it directly into the public/uploads folder:
        // return asset($this->file_path);
    }

    // ... your morphTo relationship ...
    public function fileable()
    {
        return $this->morphTo();
    }
}
