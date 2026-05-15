<?php

namespace Tests\Unit;

use App\Models\RegistroPeso;
use App\Observers\IRegistroPesoObserver;
use App\Observers\RegistroPesoSubject;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Subclase de prueba que expone el método notificar() privado.
 * Esto evita depender de la BD sin cambiar el diseño del Subject.
 */
class RegistroPesoSubjectTestable extends RegistroPesoSubject
{
    public function notificarPublico(RegistroPeso $registro): void
    {
        $metodo = new \ReflectionMethod(RegistroPesoSubject::class, 'notificar');
        $metodo->setAccessible(true);
        $metodo->invoke($this, $registro);
    }
}

class RegistroPesoSubjectTest extends TestCase
{
    private RegistroPesoSubjectTestable $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new RegistroPesoSubjectTestable;
    }

    private function crearRegistroFalso(): RegistroPeso
    {
        $registro = new RegistroPeso;
        $registro->id = 1;
        $registro->animal_id = 42;
        $registro->peso_kg = 380.5;
        $registro->metodo_usado = 'yolov8';
        $registro->confianza_porcentaje = 87.3;
        $registro->fecha_registro = now();

        return $registro;
    }

    #[Test]
    public function un_observador_suscrito_recibe_la_notificacion(): void
    {
        $observadorMock = $this->createMock(IRegistroPesoObserver::class);
        $observadorMock->expects($this->once())
            ->method('onPesoRegistrado');

        $this->subject->suscribir($observadorMock);
        $this->subject->notificarPublico($this->crearRegistroFalso());
    }

    #[Test]
    public function tres_observadores_suscritos_todos_reciben_la_notificacion(): void
    {
        $mock1 = $this->createMock(IRegistroPesoObserver::class);
        $mock2 = $this->createMock(IRegistroPesoObserver::class);
        $mock3 = $this->createMock(IRegistroPesoObserver::class);

        $mock1->expects($this->once())->method('onPesoRegistrado');
        $mock2->expects($this->once())->method('onPesoRegistrado');
        $mock3->expects($this->once())->method('onPesoRegistrado');

        $this->subject->suscribir($mock1);
        $this->subject->suscribir($mock2);
        $this->subject->suscribir($mock3);

        $this->subject->notificarPublico($this->crearRegistroFalso());
    }

    #[Test]
    public function cuatro_observadores_todos_reciben_notificacion(): void
    {
        // Demuestra que AlertaSMS (cuarto observador) funciona igual
        // sin haber modificado RegistroPesoSubject
        $mocks = [];
        for ($i = 0; $i < 4; $i++) {
            $mock = $this->createMock(IRegistroPesoObserver::class);
            $mock->expects($this->once())->method('onPesoRegistrado');
            $mocks[] = $mock;
            $this->subject->suscribir($mock);
        }

        $this->subject->notificarPublico($this->crearRegistroFalso());
        $this->assertEquals(4, $this->subject->totalObservadores());
    }

    #[Test]
    public function observador_desuscrito_no_recibe_notificacion(): void
    {
        $mock = $this->createMock(IRegistroPesoObserver::class);
        $mock->expects($this->never())->method('onPesoRegistrado');

        $this->subject->suscribir($mock);
        $this->subject->desuscribir($mock);

        $this->subject->notificarPublico($this->crearRegistroFalso());
    }

    #[Test]
    public function subject_sin_observadores_no_lanza_excepcion(): void
    {
        $this->expectNotToPerformAssertions();
        $this->subject->notificarPublico($this->crearRegistroFalso());
    }

    #[Test]
    public function conteo_de_observadores_es_correcto(): void
    {
        $this->assertEquals(0, $this->subject->totalObservadores());

        $mock1 = $this->createMock(IRegistroPesoObserver::class);
        $mock2 = $this->createMock(IRegistroPesoObserver::class);

        $this->subject->suscribir($mock1);
        $this->subject->suscribir($mock2);

        $this->assertEquals(2, $this->subject->totalObservadores());

        $this->subject->desuscribir($mock1);
        $this->assertEquals(1, $this->subject->totalObservadores());
    }
}
