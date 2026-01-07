<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }

    // --- NUEVO CÓDIGO PARA ROLES ---

    // Relación con la tabla Roles
    public function roles(): BelongsToMany
    {
        // Relacionamos el modelo User con el modelo Role a través de usuarioroles
        return $this->belongsToMany(Role::class, 'usuarioroles', 'usuario_id', 'rol_id');
    }

    // Verificar si tiene un rol específico (ej: "Empleado")
    public function hasRole($roleName)
    {
        return $this->roles()->where('nombre', $roleName)->exists();
    }

    // Verificar si es Super Admin (Acceso total)
    public function isSuperAdmin()
    {
        return $this->hasRole('Super Admin');
    }
}