<?php

namespace App\Models;

use App\Enums\PostType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory;

    protected $appends = ['content'];

    protected $fillable = [
        'institution_id',
        'user_id',
        'category_id',
        'type',
        'title',
        'description',
        'content',
        'start_at',
        'end_at',
        'location',
        'capacity',
    ];

    protected function casts(): array
    {
        return [
            'type' => PostType::class,
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'capacity' => 'integer',
        ];
    }

    public function setUserIdAttribute(int $userId): void
    {
        $this->attributes['institution_id'] = $userId;
    }

    public function setContentAttribute(?string $content): void
    {
        $this->attributes['description'] = $content;
    }

    public function getContentAttribute(): ?string
    {
        return $this->attributes['description'] ?? null;
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(User::class, 'institution_id');
    }

    public function user(): BelongsTo
    {
        return $this->institution();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function eventParticipations(): HasMany
    {
        return $this->hasMany(EventParticipation::class);
    }

    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_posts')->withTimestamps();
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
