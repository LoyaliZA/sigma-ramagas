<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = false; // Tu tabla roles no tiene created_at/updated_at
    protected $fillable = ['nombre', 'descripcion'];
}