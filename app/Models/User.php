<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'subscription_plan',
        'plan_started_at',
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
            'plan_started_at' => 'datetime',
        ];
    }

    /**
     * User has many tasks.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }

        // Default avatar (initials)
        return '';
    }

    /**
     * Get subscription plan label
     */
    public function getPlanLabelAttribute(): string
    {
        return match ($this->subscription_plan) {
            'free' => 'Free Plan',
            'pro' => 'Pro Plan',
            'plus' => 'Plus Plan',
            default => 'Free Plan',
        };
    }

    /**
     * Get plan features
     */
    public function getPlanFeaturesAttribute(): array
    {
        return match ($this->subscription_plan) {
            'free' => [
                'voice_recorder' => false,
                'max_collaborators' => 3,
                'price' => 0,
            ],
            'pro' => [
                'voice_recorder' => true,
                'max_collaborators' => 10,
                'price' => 45000, // Rp 45.000
            ],
            'plus' => [
                'voice_recorder' => true,
                'max_collaborators' => 30,
                'price' => 65000, // Rp 65.000
            ],
            default => [
                'voice_recorder' => false,
                'max_collaborators' => 3,
                'price' => 0,
            ],
        };
    }

    /**
     * Check if user can access voice recorder
     */
    public function canUseVoiceRecorder(): bool
    {
        return $this->plan_features['voice_recorder'];
    }

    /**
     * Get max collaborators allowed
     */
    public function getMaxCollaborators(): int
    {
        return $this->plan_features['max_collaborators'];
    }

    public function files()
    {
        return $this->hasMany(UserFile::class);
    }
}
