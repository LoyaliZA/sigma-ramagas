<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoTipoAlmacenamiento extends Model
{
    use HasFactory;

    protected $table = 'catalogo_tipos_almacenamiento';
    public $timestamps = false;

    protected $fillable = ['nombre'];
}