<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoMarca extends Model
{
    use HasFactory;

    protected $table = 'catalogo_marcas';
    public $timestamps = false;

    protected $fillable = ['nombre'];
}