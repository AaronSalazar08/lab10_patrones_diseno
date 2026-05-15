<?php

namespace Tests\Unit;

use App\Domain\Razas\Angus;
use App\Domain\Razas\Brahman;
use App\Domain\Razas\Nelore;
use App\Factories\RazaFactory;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RazaFactoryTest extends TestCase
{
    private RazaFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new RazaFactory();
    }

    #[Test]
    public function crea_instancia_brahman_correctamente(): void
    {
        $raza = $this->factory->create('brahman');
        $this->assertInstanceOf(Brahman::class, $raza);
        $this->assertEquals('Brahman', $raza->getNombre());
        $this->assertEquals(1.05, $raza->getFactorCorreccionPeso());
    }

    #[Test]
    public function crea_instancia_nelore_correctamente(): void
    {
        $raza = $this->factory->create('nelore');
        $this->assertInstanceOf(Nelore::class, $raza);
        $this->assertEquals('Nelore', $raza->getNombre());
        $this->assertEquals(1.02, $raza->getFactorCorreccionPeso());
    }

    #[Test]
    public function crea_instancia_angus_sin_modificar_factory(): void
    {
        // Esta prueba demuestra que Angus fue agregado SIN tocar la lógica
        // de la factory, solo añadiendo la clase y una línea al array $mapa
        $raza = $this->factory->create('angus');
        $this->assertInstanceOf(Angus::class, $raza);
        $this->assertEquals('Angus', $raza->getNombre());
        $this->assertEquals(1.08, $raza->getFactorCorreccionPeso());
    }

    #[Test]
    public function acepta_nombre_raza_en_mayusculas(): void
    {
        $raza = $this->factory->create('BRAHMAN');
        $this->assertInstanceOf(Brahman::class, $raza);
    }

    #[Test]
    public function acepta_nombre_raza_con_espacios(): void
    {
        $raza = $this->factory->create('  nelore  ');
        $this->assertInstanceOf(Nelore::class, $raza);
    }

    #[Test]
    public function lanza_excepcion_para_raza_no_registrada(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Holstein/');
        $this->factory->create('Holstein');
    }

    #[Test]
    public function retorna_lista_de_razas_disponibles(): void
    {
        $razas = $this->factory->getRazasDisponibles();
        $this->assertContains('brahman', $razas);
        $this->assertContains('nelore', $razas);
        $this->assertContains('angus', $razas);
    }
}
