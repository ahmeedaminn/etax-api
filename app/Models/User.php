<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HAT 1: THE TARGET (Profile Pictures)
    |--------------------------------------------------------------------------
    | This uses the polymorphic `fileable_id` and `fileable_type` columns.
    | It grabs the image specifically attached to the user's profile.
    */
    public function profilePicture()
    {
        return $this->morphOne(\App\Models\File::class, 'fileable')->latestOfMany();
    }

    /*
    |--------------------------------------------------------------------------
    | HAT 2: THE UPLOADER
    |--------------------------------------------------------------------------
    | This uses the standard `user_id` column on the files table.
    | It grabs EVERY file this user has ever uploaded (Post images, 
    | Category covers, Profile pics, etc).
    */
    public function uploadedFiles()
    {
        return $this->hasMany(\App\Models\File::class, 'user_id');
    }
}
