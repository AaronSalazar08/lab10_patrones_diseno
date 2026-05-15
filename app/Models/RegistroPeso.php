<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroPeso extends Model
{
    use HasFactory;

    protected $table = 'registros_peso';

    protected $fillable = [
        'animal_id',
        'peso_kg',
        'confianza_porcentaje',
        'metodo_usado',
        'foto_path',
        'fecha_registro',
    ];

    protected $casts = [
        'peso_kg' => 'float',
        'confianza_porcentaje' => 'float',
        'animal_id' => 'integer',
        'fecha_registro' => 'datetime',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class, 'animal_id');
    }
}
