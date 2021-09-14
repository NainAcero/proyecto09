<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cajon extends Model {
    use HasFactory;

    protected $table = 'cajones';

    protected $fillable = ['descripcion', 'tipo_id', 'status'];
}
