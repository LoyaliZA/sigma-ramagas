<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoEstadoActivo extends Model
{
    use HasFactory;

    protected $table = 'catalogo_estadosactivo';
    public $timestamps = false;

    protected $fillable = ['nombre'];
}