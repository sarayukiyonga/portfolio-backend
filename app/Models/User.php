<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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
        ];
    }

    /**
     * Get the roles that belong to the user.
     */

public function roles(): BelongsToMany
{
    return $this->belongsToMany(Role::class, 'user_role'); // Especificar nombre de tabla
}

public function hasRole(string|array $role): bool
{
    if (is_string($role)) {
        return $this->roles->contains('name', $role);
    }

    if (is_array($role)) {
        foreach ($role as $r) {
            if ($this->hasRole($r)) {
                return true;
            }
        }
        return false;
    }

    return false;
}

public function assignRole(string $role): void
{
    $roleModel = Role::where('name', $role)->firstOrFail();
    $this->roles()->syncWithoutDetaching($roleModel);
}

public function removeRole(string $role): void
{
    $roleModel = Role::where('name', $role)->firstOrFail();
    $this->roles()->detach($roleModel);
}

public function isAdmin(): bool
{
    return $this->hasRole('admin');
}
}
