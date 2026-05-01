<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'language',
        'bio',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'favorites')->withTimestamps();
    }

    public function chatsAsStudent(): HasMany
    {
        return $this->hasMany(Chat::class, 'student_id');
    }

    public function chatsAsOwner(): HasMany
    {
        return $this->hasMany(Chat::class, 'owner_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Helper methods
    public function isStudent(): bool
    {
        return $this->role?->name === 'student';
    }

    public function isOwner(): bool
    {
        return $this->role?->name === 'owner';
    }

    public function isBroker(): bool
    {
        return $this->role?->name === 'broker';
    }

    public function canManageListings(): bool
    {
        return $this->isOwner() || $this->isBroker();
    }
}
