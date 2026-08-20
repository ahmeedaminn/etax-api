<?php

namespace App\Models;

use App\Enums\InstitutionRequestStatus;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'institution_request_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Get primary key identifier for the JWT token
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // Return custom key-value claims to add to the JWT
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    // Automatically casts the password to a hashed string before saving to the DB
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => UserRole::class,
            'institution_request_status' => InstitutionRequestStatus::class,
        ];
    }

    public function institutionProfile(): HasOne
    {
        return $this->hasOne(InstitutionProfile::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'institution_id');
    }

    public function eventParticipations(): HasMany
    {
        return $this->hasMany(EventParticipation::class);
    }

    public function savedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'saved_posts')->withTimestamps();
    }

    public function profilePicture(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable')->latestOfMany();
    }

    public function uploadedFiles(): HasMany
    {
        return $this->hasMany(File::class, 'user_id');
    }
}
