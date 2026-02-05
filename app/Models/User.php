<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\HasName;
use Filament\Panel;  // Add this import
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser;  // Add this import

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $connection = 'fms';
    protected $table = 'systemusers';
    protected $primaryKey = 'recid';
    public $timestamps = false;

    protected $casts = [
        'is_active' => 'boolean',
        'laravel_password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active == 1;
    }

    public function getAuthIdentifierName()
    {
        return 'recid';
    }

    public function getAuthPassword()
    {
        // Use laravel_password if it exists, otherwise fall back to UserPassword
        return $this->laravel_password ?? $this->UserPassword;
    }

    protected $fillable = [
        'FullName',
        'Designation',
        'UserName',
        'UserPassword',
        'laravel_password',
        'UserType',
        'email',
        'department_code',
        'is_active',
    ];

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
}
