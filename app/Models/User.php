<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * Constant Variable 
     * 
     * @var object
     */
    const GENDER_TYPE = [
        0 => 'Female',
        1 => 'Male'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nim',
        'npa',
        'name_bagus',
        'picture',
        'year',
        'device_token',
        'gender',
        'dbu_id',
        'cabinet_id',
        'puzzle_level',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The attributes that are logged.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'nim', 'npa', 'name_bagus', 'picture', 'year', 'device_token', 'cabinet_id', 'dbu_id', 'gender','puzzle_level'])
            ->logOnlyDirty()
            ->useLogName('User')
            ->setDescriptionForEvent(function (string $eventName) {
                return "{$this->name} has been {$eventName}";
            });
    }

    /**
     * The attributes should be casted to native types.
     * 
     * @return Attribute
     */
    protected function picture(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (file_exists(storage_path('app/public/' . config('dirpath.users.pictures') . '/' . $value)) && $value != null) {
                    return asset('storage/' . config('dirpath.users.pictures') . '/' . $value);
                } else if ($this->gender == 1) {
                    return asset(config('tablar.default.male_avatar.path'));
                } else if ($this->gender == 0) {
                    return asset(config('tablar.default.female_avatar.path'));
                }
            },
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS 
    |--------------------------------------------------------------------------
    |
    | Here are the relations this model has with other models
    |
    */

    /**
     * Get the user's role.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cabinet()
    {
        return $this->belongsTo(Cabinet::class, 'cabinet_id');
    }

    /**
     * Get the user's role.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dbu()
    {
        return $this->belongsTo(DBU::class, 'dbu_id');
    }

    /**
     * Get the user's program.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'users_programs', 'user_id', 'program_id');
    }

    /**
     * Get the user's program.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function program()
    {
        return $this->hasMany(Program::class, 'user_id');
    }

    /**
     * Get the user's notifications.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'users_notifications', 'user_id', 'notification_id')->withPivot('id')->withTimestamps();
    }

    /**
     * Get the user's work history.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workHistories()
    {
        return $this->hasMany(WorkHistory::class, 'user_id');
    }

    /**
     * Get the user's complaints.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'user_id');
    }
}
