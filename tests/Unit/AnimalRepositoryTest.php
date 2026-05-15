<?php

namespace Tests\Unit;

use App\Models\Animal;
use App\Repositories\InMemoryAnimalRepository;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AnimalRepositoryTest extends TestCase
{
    private InMemoryAnimalRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new InMemoryAnimalRepository;
    }

    protected function tearDown(): void
    {
        $this->repo->limpiar();
        parent::tearDown();
    }

    private function crearAnimal(string $arete, int $ranchoId, string $raza = 'brahman'): Animal
    {
        $animal = new Animal;
        $animal->arete = $arete;
        $animal->rancho_id = $ranchoId;
        $animal->raza = $raza;
        $animal->sexo = 'macho';
        $animal->estado = 'activo';

        return $animal;
    }

    #[Test]
    public function guarda_y_recupera_animal_por_arete(): void
    {
        $animal = $this->crearAnimal('CR-001', 1);
        $this->repo->save($animal);

        $encontrado = $this->repo->findByArete('CR-001');

        $this->assertNotNull($encontrado);
        $this->assertEquals('CR-001', $encontrado->arete);
    }

    #[Test]
    public function retorna_null_para_arete_inexistente(): void
    {
        $resultado = $this->repo->findByArete('CR-999');
        $this->assertNull($resultado);
    }

    #[Test]
    public function retorna_solo_animales_del_rancho_indicado(): void
    {
        $this->repo->save($this->crearAnimal('CR-001', 1));
        $this->repo->save($this->crearAnimal('CR-002', 1));
        $this->repo->save($this->crearAnimal('CR-003', 2)); // rancho diferente

        $animalesRancho1 = $this->repo->findAllByRancho(1);

        $this->assertCount(2, $animalesRancho1);
    }

    #[Test]
    public function elimina_animal_correctamente(): void
    {
        $animal = $this->crearAnimal('CR-001', 1);
        $this->repo->save($animal);

        $this->repo->delete($animal->id);

        $this->assertNull($this->repo->findByArete('CR-001'));
    }

    #[Test]
    public function retorna_solo_animales_activos(): void
    {
        $activo = $this->crearAnimal('CR-001', 1);
        $vendido = $this->crearAnimal('CR-002', 1);
        $vendido->estado = 'vendido';

        $this->repo->save($activo);
        $this->repo->save($vendido);

        $activos = $this->repo->findAllActivos();

        $this->assertCount(1, $activos);
        $this->assertEquals('CR-001', $activos[0]->arete);
    }

    #[Test]
    public function no_retorna_animales_de_rancho_diferente_en_find_all_by_rancho(): void
    {
        $this->repo->save($this->crearAnimal('CR-010', 5));

        $resultado = $this->repo->findAllByRancho(99);

        $this->assertEmpty($resultado);
    }
}
