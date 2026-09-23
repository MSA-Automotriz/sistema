<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación muchos a muchos con roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Verifica si el usuario tiene un rol específico
     */
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Asigna un rol al usuario
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        
        return $this->roles()->syncWithoutDetaching($role);
    }

    /**
     * Remueve un rol del usuario
     */
    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        return $this->roles()->detach($role);
    }

    /**
     * Scope para filtrar usuarios por rol
     */
    public function scopeRole($query, $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            if (is_numeric($role)) {
                $q->where('roles.id', $role);
            } else {
                $q->where('roles.name', $role);
            }
        });
    }

    /**
     * Verifica si el usuario tiene un permiso a través de sus roles
     */
    public function hasPermission($permission)
    {
        $permissionName = is_string($permission) ? $permission : $permission->name;

        // Un usuario con rol admin tiene acceso a todo
        if ($this->hasRole('admin')) {
            return true;
        }

        // Verificar si el permiso existe en alguno de sus roles
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->exists();
    }
}