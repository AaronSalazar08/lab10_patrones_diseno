<?php

namespace App\Strategies;

use App\Strategies\Concretas\AlgoritmoRegresionLineal;
use App\Strategies\Concretas\AlgoritmoTablaReferencia;
use App\Strategies\Concretas\AlgoritmoYolov8;
use InvalidArgumentException;

class EstimadorFactory
{
    /** @var array<string, class-string<IAlgoritmoEstimacion>> */
    private array $estrategias = [
        'yolov8' => AlgoritmoYolov8::class,
        'regresion' => AlgoritmoRegresionLineal::class,
        'tabla' => AlgoritmoTablaReferencia::class,
    ];

    public function crear(string $metodo): IAlgoritmoEstimacion
    {
        $clave = strtolower(trim($metodo));

        if (! array_key_exists($clave, $this->estrategias)) {
            throw new InvalidArgumentException(
                "Método '{$metodo}' no reconocido. "
                .'Métodos disponibles: '.implode(', ', array_keys($this->estrategias))
            );
        }

        $clase = $this->estrategias[$clave];

        return new $clase;
    }
}
