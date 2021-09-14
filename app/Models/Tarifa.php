<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model {
    use HasFactory;

    protected $table = 'tarifas';

    protected $fillable = ['tiempo', 'descripcion', 'costo', 'tipo_id', 'jerarquia'];

    public function tipo(){
    	return $this->HasOne(Tipo::class, 'id', 'tipo_id');
    }
}
