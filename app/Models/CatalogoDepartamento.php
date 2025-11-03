<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoDepartamento extends Model
{
    use HasFactory;
    
    protected $table = 'catalogo_departamentos';
    public $timestamps = false;
    protected $fillable = ['nombre'];
}
