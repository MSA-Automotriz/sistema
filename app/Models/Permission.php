<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'module',
    ];

    /**
     * Relación muchos a muchos con roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * Scope para permisos por módulo
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module)->orderBy('display_name');
    }

    /**
     * Obtener todos los permisos agrupados por módulo
     */
    public static function groupedByModule()
    {
        return self::orderBy('module')->orderBy('display_name')->get()->groupBy('module');
    }
}
