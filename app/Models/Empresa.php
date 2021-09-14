<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model {
    use HasFactory;

    protected $fillable = ['nombre', 'telefono', 'email', 'direccion', 'logo'];
    protected $table = 'empresas';
}
