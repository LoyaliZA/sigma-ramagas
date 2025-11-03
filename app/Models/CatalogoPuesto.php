<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoPuesto extends Model
{
    use HasFactory;

    protected $table = 'catalogo_puestos';
    public $timestamps = false;
    protected $fillable = ['nombre'];
}
