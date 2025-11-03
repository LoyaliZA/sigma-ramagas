<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoUbicacion extends Model
{
    use HasFactory;

    protected $table = 'catalogo_ubicaciones';
    public $timestamps = false;
    protected $fillable = ['nombre'];
}
