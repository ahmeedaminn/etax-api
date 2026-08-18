<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class InstitutionProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_name',
        'description',
        'website',
        'contact_email',
        'contact_phone',
        'location',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'institution_id', 'user_id');
    }

    public function logo(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable')->latestOfMany();
    }
}
