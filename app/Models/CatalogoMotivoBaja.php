<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoMotivoBaja extends Model
{
    protected $table = 'catalogo_motivosbaja';
    protected $fillable = ['nombre'];
    public $timestamps = false;
}