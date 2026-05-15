<?php

namespace Tests\Unit;

use App\Domain\ResultadoEstimacion;
use App\Services\EstimadorPesoService;
use App\Strategies\Concretas\AlgoritmoRegresionLineal;
use App\Strategies\Concretas\AlgoritmoTablaReferencia;
use App\Strategies\Concretas\AlgoritmoYolov8;
use App\Strategies\IAlgoritmoEstimacion;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EstimadorPesoServiceTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $datosBase = [
        'raza' => 'brahman',
        'peso_referencia_kg' => 350.0,
    ];

    #[Test]
    public function estima_peso_con_algoritmo_inyectado(): void
    {
        $mockAlgoritmo = $this->createMock(IAlgoritmoEstimacion::class);
        $mockAlgoritmo->method('estaDisponible')->willReturn(true);
        $mockAlgoritmo->method('ejecutar')->willReturn(
            new ResultadoEstimacion(360.5, 90.0, 'mock')
        );

        $servicio = new EstimadorPesoService($mockAlgoritmo);
        $resultado = $servicio->estimar($this->datosBase);

        $this->assertEquals(360.5, $resultado->pesoKg);
        $this->assertEquals('mock', $resultado->metodoUsado);
    }

    #[Test]
    public function hace_fallback_a_tabla_cuando_algoritmo_no_disponible(): void
    {
        $yolov8Caido = new AlgoritmoYolov8(
            urlServicio: 'http://ml-service:5000/estimar',
            servicioActivo: false
        );

        $servicio = new EstimadorPesoService($yolov8Caido);
        $resultado = $servicio->estimar($this->datosBase);

        $this->assertEquals('tabla', $resultado->metodoUsado);
    }

    #[Test]
    public function resultado_es_inmutable(): void
    {
        $resultado = new ResultadoEstimacion(380.0, 85.0, 'yolov8');

        $this->expectException(\Error::class);
        $resultado->pesoKg = 999.0;
    }

    #[Test]
    public function resultado_describe_correctamente(): void
    {
        $resultado = new ResultadoEstimacion(380.0, 85.0, 'yolov8');
        $descripcion = $resultado->describir();

        $this->assertStringContainsString('yolov8', $descripcion);
        $this->assertStringContainsString('380.00', $descripcion);
        $this->assertStringContainsString('85.0', $descripcion);
    }

    #[Test]
    public function resultado_es_confiable_con_confianza_alta(): void
    {
        $confiable = new ResultadoEstimacion(380.0, 85.0, 'yolov8');
        $noConfiable = new ResultadoEstimacion(380.0, 60.0, 'tabla');

        $this->assertTrue($confiable->esConfiable());
        $this->assertFalse($noConfiable->esConfiable());
    }

    #[Test]
    public function cambia_estrategia_en_tiempo_de_ejecucion(): void
    {
        $servicio = new EstimadorPesoService(new AlgoritmoYolov8);

        $servicio->cambiarAlgoritmo(new AlgoritmoRegresionLineal);
        $resultado = $servicio->estimar($this->datosBase);

        $this->assertEquals('regresion', $resultado->metodoUsado);
    }

    #[Test]
    public function algoritmo_tabla_siempre_disponible(): void
    {
        $tabla = new AlgoritmoTablaReferencia;
        $this->assertTrue($tabla->estaDisponible());
    }

    #[Test]
    public function algoritmo_regresion_siempre_disponible(): void
    {
        $regresion = new AlgoritmoRegresionLineal;
        $this->assertTrue($regresion->estaDisponible());
    }

    #[Test]
    public function yolov8_no_disponible_cuando_servicio_caido(): void
    {
        $yolov8 = new AlgoritmoYolov8(
            urlServicio: 'http://ml-service:5000/estimar',
            servicioActivo: false
        );

        $this->assertFalse($yolov8->estaDisponible());
    }
}
