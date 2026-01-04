<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoCondicion extends Model
{
    use HasFactory;

    protected $table = 'catalogo_condiciones';
    public $timestamps = false;

    protected $fillable = ['nombre'];
}