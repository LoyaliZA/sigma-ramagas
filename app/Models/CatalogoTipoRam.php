<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoTipoRam extends Model
{
    use HasFactory;

    protected $table = 'catalogo_tipos_ram';
    public $timestamps = false;

    protected $fillable = ['nombre'];
}

