<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Achievement;
use App\Models\Todo;
use App\Models\UserAchievement;
use App\Models\UserBadge;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'weekly_goal',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_image_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'weekly_goal' => 'integer',
    ];

    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }

    public function userBadges(): HasMany
    {
        return $this->hasMany(UserBadge::class)->latest('earned_at');
    }

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot(['progress', 'unlocked_at', 'is_visible'])
            ->withTimestamps();
    }

    public function getProfileImageUrlAttribute(): string
    {
        $defaultAvatar = url('/images/default-avatar.svg');

        if (empty($this->profile_image)) {
            return $defaultAvatar;
        }

        $disk = config('profile.image_disk', 'public');
        $filesystem = app('filesystem')->disk($disk);

        if (filter_var($this->profile_image, FILTER_VALIDATE_URL)) {
            $baseUrl = $this->profile_image;
        } elseif ($disk === 'public') {
            $baseUrl = url('/storage/'.ltrim($this->profile_image, '/'));
        } else {
            $baseUrl = is_object($filesystem) && method_exists($filesystem, 'url')
                ? $filesystem->url($this->profile_image)
                : url('/storage/'.ltrim($this->profile_image, '/'));
        }

        $version = $this->updated_at?->timestamp ?? now()->timestamp;
        $separator = str_contains($baseUrl, '?') ? '&' : '?';

        return "{$baseUrl}{$separator}v={$version}";
    }
}
