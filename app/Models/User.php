<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use Filament\Models\Contracts\FilamentUser;  // Add this import
use Filament\Models\Contracts\HasName;
use Filament\Panel;  // Add this import
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasFactory, Notifiable;

    protected $connection = 'fms';
    protected $table = 'systemusers';
    protected $primaryKey = 'recid';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guard_name = 'web';

    protected $casts = [
        'is_active' => 'boolean',
        // 'laravel_password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getAuthIdentifierName()
    {
        return 'recid';
    }

    public function getAuthPassword()
    {
        // Use laravel_password if it exists, otherwise fall back to UserPassword
        return $this->laravel_password;
    }

    protected $guarded = ['recid'];

    protected $hidden = [
        'UserPassword',
        'laravel_password',
        'remember_token',
    ];


    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function getFilamentName(): string
    {
        // Fallback to a placeholder string if both are null
        return (string) ($this->FullName ?: $this->UserName ?: 'Unknown User');
    }


    // /**
    //  * The attributes that are mass assignable.
    //  *
    //  * @var list<string>
    //  */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    //     'department_code',
    // ];

    // /**
    //  * The attributes that should be hidden for serialization.
    //  *
    //  * @var list<string>
    //  */
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    // /**
    //  * Get the attributes that should be cast.
    //  *
    //  * @return array<string, string>
    //  */
    // protected function casts(): array
    // {
    //     return [
    //         'department_code' => 'integer',
    //         'email_verified_at' => 'datetime',
    //         'password' => 'hashed',
    //     ];
    // }
    public function setPasswordInputAttribute($value)
    {
        if (!empty($value)) {
            // Save MD5 version
            $this->attributes['UserPassword'] = md5($value);

            // Save Laravel hashed version (auto bcrypt because of cast)
            $this->attributes['laravel_password'] = Hash::make($value);
        }
    }

    public function officeRelation()
    {
        return $this->belongsTo(Office::class, 'department_code', 'department_code');
    }
  
    public function roles(): MorphToMany
    {
        $instance = $this->newRelatedInstance(Role::class);

        $instance->setConnection('mysql');

        return $this->morphToMany(
            Role::class,
            'model',
            'model_has_roles',
            'model_id',
            'role_id'
        );
    }

    public function permissions(): MorphToMany
    {
        $instance = $this->newRelatedInstance(Permission::class);

        $instance->setConnection('mysql');

        return $this->morphToMany(
            Permission::class,
            'model',
            'model_has_permissions',
            'model_id',
            'permission_id'
        );
    }
}
