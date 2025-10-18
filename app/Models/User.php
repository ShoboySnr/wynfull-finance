<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'activated_at',
        'activated_by_id',
        'onboarding_completed',
        'onboarding_completed_at',
    ];

    protected static array $logAttributes = ['name', 'email'];
    protected static string $logName = 'user';
    protected static bool $logOnlyDirty = true;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'onboarding_completed' => 'boolean',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('user');
    }

    public function clientProfile(): HasOne
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function coachProfile(): HasOne
    {
        return $this->hasOne(CoachProfile::class);
    }


    public function coaches(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'coach_client_assignments',
            'client_id',
            'coach_id'
        )->withTimestamps()
            ->withPivot(['assigned_by', 'assigned_at', 'status', 'id']);
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'coach_client_assignments',
            'coach_id',
            'client_id'
        )->withTimestamps()
            ->withPivot(['assigned_by', 'assigned_at', 'status', 'id']);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }
}
