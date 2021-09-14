<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renta extends Model {
    use HasFactory;

    protected $fillable = [
        'acceso', 'hours', 'salida', 'placa', 'modelo', 'marca', 'color', 'llaves',
        'total', 'efectivo', 'cambio', 'user_id', 'vehiculo_id', 'tarifa_id','barcode',
        'status','descripcion','cajon_id'
    ];

    protected $table = 'rentas';

    public function tarifa() {
    	return $this->belongsTo(Tarifa::class);
    }
}
