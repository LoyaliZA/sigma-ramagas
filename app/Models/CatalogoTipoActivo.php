<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoTipoActivo extends Model
{
    use HasFactory;

    protected $table = 'catalogo_tiposactivo';
    public $timestamps = false;

    protected $fillable = ['nombre'];
}