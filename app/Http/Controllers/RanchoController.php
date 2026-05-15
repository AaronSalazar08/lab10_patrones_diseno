<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Repositories\IAnimalRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RanchoController extends Controller
{
    public function __construct(
        private readonly IAnimalRepository $animalRepository
    ) {}

    /**
     * Lista todos los animales activos de un rancho.
     */
    public function animales(int $ranchoId): JsonResponse
    {
        $animales = $this->animalRepository->findAllByRancho($ranchoId);

        return response()->json([
            'rancho_id' => $ranchoId,
            'total' => count($animales),
            'animales' => $animales,
        ]);
    }

    /**
     * Busca un animal por arete.
     */
    public function buscarPorArete(string $arete): JsonResponse
    {
        $animal = $this->animalRepository->findByArete($arete);

        if (! $animal) {
            return response()->json(['error' => "Animal con arete '{$arete}' no encontrado."], 404);
        }

        return response()->json($animal);
    }

    /**
     * Guarda un nuevo animal usando el repositorio.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'arete' => 'required|string|max:20',
            'raza' => 'required|string',
            'rancho_id' => 'required|integer',
            'sexo' => 'required|in:macho,hembra',
            'nombre' => 'nullable|string|max:100',
        ]);

        $animal = new Animal;
        $animal->arete = $request->input('arete');
        $animal->raza = strtolower($request->input('raza'));
        $animal->rancho_id = $request->input('rancho_id');
        $animal->sexo = $request->input('sexo');
        $animal->nombre = $request->input('nombre');
        $animal->estado = 'activo';

        $this->animalRepository->save($animal);

        return response()->json([
            'mensaje' => 'Animal guardado correctamente.',
            'animal' => $animal,
        ], 201);
    }

    /**
     * Elimina un animal por ID.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->animalRepository->delete($id);

        return response()->json(['mensaje' => 'Animal eliminado correctamente.']);
    }
}
