<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoEstadoAsignacion extends Model
{
    use HasFactory;

    protected $table = 'catalogo_estadosasignacion';
    public $timestamps = false;

    protected $fillable = ['nombre'];
}