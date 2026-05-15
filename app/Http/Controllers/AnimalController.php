<?php

namespace App\Http\Controllers;

use App\Factories\IRazaFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnimalController extends Controller
{
    public function __construct(
        private readonly IRazaFactory $razaFactory
    ) {}

    /**
     * Registra un animal aplicando el factor de corrección de su raza.
     * Antes: new Brahman() hardcodeado aquí.
     * Ahora: la factory decide qué clase instanciar.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre_animal' => 'required|string|max:100',
            'raza' => 'required|string',
            'peso_foto_kg' => 'required|numeric|min:1',
        ]);

        try {
            $raza = $this->razaFactory->create($request->input('raza'));
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }

        $pesoFotoKg = (float) $request->input('peso_foto_kg');
        $pesoEstimado = round($pesoFotoKg * $raza->getFactorCorreccionPeso(), 2);

        return response()->json([
            'mensaje' => 'Animal registrado correctamente.',
            'animal' => $request->input('nombre_animal'),
            'raza' => $raza->getNombre(),
            'descripcion_raza' => $raza->describir(),
            'peso_foto_kg' => $pesoFotoKg,
            'peso_estimado_kg' => $pesoEstimado,
        ], 201);
    }

    /**
     * Lista las razas disponibles en el sistema.
     * El controlador no sabe cuántas razas hay; la factory lo sabe.
     */
    public function razasDisponibles(): JsonResponse
    {
        /** @var \App\Factories\RazaFactory $factory */
        $factory = $this->razaFactory;

        return response()->json([
            'razas_disponibles' => method_exists($factory, 'getRazasDisponibles')
                ? $factory->getRazasDisponibles()
                : [],
        ]);
    }
}
