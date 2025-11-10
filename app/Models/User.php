<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, LogsActivity, TwoFactorAuthenticatable;

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
        'email_notifications_enabled',
        'goal_reminders_enabled'
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
            'password_changed_at' => 'datetime',
            'email_notifications_enabled' => 'boolean',
            'goal_reminders_enabled'      => 'boolean',
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

    public function coachAssignmentsAsClient(): HasMany
    {
        return $this->hasMany(CoachClientAssignment::class, 'client_id');
    }

    public function clientAssignmentsAsCoach(): HasMany
    {
        return $this->hasMany(CoachClientAssignment::class, 'coach_id');
    }
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(CoachAvailabilitySetting::class, 'coach_id');
    }

    public function availabilitySetting(): HasOne
    {
        return $this->hasOne(CoachAvailabilitySetting::class, 'coach_id');
    }

    public function moduleCompletions(): BelongsToMany
    {
        return $this->belongsToMany(ResourceModule::class, 'resource_module_users')
            ->withPivot(['completed_at'])
            ->withTimestamps();
    }

    public function completedModules(): BelongsToMany
    {
        return $this->moduleCompletions()->wherePivotNotNull('completed_at');
    }

    public function hasCompletedModule(int $moduleId): bool
    {
        return $this->completedModules()->where('resource_module_id', $moduleId)->exists();
    }

    public function directlyAssignedModules(): BelongsToMany
    {
        return $this->belongsToMany(ResourceModule::class, 'resource_module_assignments', 'user_id', 'resource_module_id')
            ->withPivot(['assigned_by','assigned_at'])
            ->withTimestamps();
    }
}
