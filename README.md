# BovWeight CR — Catálogo de Patrones de Diseño

**Laboratorio 10 — Ingeniería de Software**  
**Universidad de Costa Rica | I Semestre 2026**

Sistema de estimación de peso de ganado bovino mediante fotografías, usado como contexto para demostrar la implementación de cuatro patrones de diseño en un proyecto Laravel 13 real.

---

## Stack tecnológico

| Tecnología | Versión |
|---|---|
| PHP | 8.3 |
| Laravel Framework | 13 |
| PHPUnit | 12 |
| Laravel Pint | 1 |
| Base de datos (tests) | SQLite in-memory |

---

## Patrones implementados

### 1. Factory Method

**Problema resuelto:** Los controladores instanciaban razas directamente con `new Brahman()` o `new Nelore()` en al menos 6 puntos distintos del código. Cualquier cambio de lógica requería modificar múltiples archivos.

**Solución:** Una interfaz `IRazaFactory` con un `ConcreteCreator` (`RazaFactory`) que centraliza la creación usando un array asociativo como mapa de razas. Agregar una nueva raza (`Angus`) solo requirió crear la clase y añadir una línea al mapa, sin tocar ningún controlador.

**Participantes:**

| Clase | Rol | Ruta |
|---|---|---|
| `Raza` | Product abstracto | `app/Domain/Raza.php` |
| `Brahman` | ConcreteProduct | `app/Domain/Razas/Brahman.php` |
| `Nelore` | ConcreteProduct | `app/Domain/Razas/Nelore.php` |
| `Angus` | ConcreteProduct (extensibilidad) | `app/Domain/Razas/Angus.php` |
| `IRazaFactory` | Creator (interfaz) | `app/Factories/IRazaFactory.php` |
| `RazaFactory` | ConcreteCreator | `app/Factories/RazaFactory.php` |

**Rutas API:**

```
POST   /api/animales   → AnimalController@store
GET    /api/razas      → AnimalController@razasDisponibles
```

**Pruebas:** `tests/Unit/RazaFactoryTest.php` — 7 pruebas

---

### 2. Repository

**Problema resuelto:** La consulta `Animal::where('rancho_id', $id)->with('registrosPeso')->get()` aparecía repetida en `ReporteService`, `EstimadorService` y `DashboardController`. Agregar caché Redis habría requerido modificar los 3 puntos.

**Solución:** La interfaz `IAnimalRepository` define el contrato con lenguaje del dominio ganadero (`findByArete`, `findAllByRancho`), sin rastro de Eloquent en la capa de negocio. En producción se usa `EloquentAnimalRepository`; en tests se usa `InMemoryAnimalRepository` (puro PHP, sin BD).

**Participantes:**

| Clase | Rol | Ruta |
|---|---|---|
| `IAnimalRepository` | Repository Interface | `app/Repositories/IAnimalRepository.php` |
| `EloquentAnimalRepository` | Concrete Repository (producción) | `app/Repositories/EloquentAnimalRepository.php` |
| `InMemoryAnimalRepository` | In-Memory Repository (tests) | `app/Repositories/InMemoryAnimalRepository.php` |
| `Animal` | Modelo Eloquent | `app/Models/Animal.php` |

**Rutas API:**

```
GET    /api/ranchos/{ranchoId}/animales   → RanchoController@animales
GET    /api/animales/arete/{arete}        → RanchoController@buscarPorArete
POST   /api/ranchos/animales              → RanchoController@store
DELETE /api/animales/{id}                 → RanchoController@destroy
```

**Pruebas:** `tests/Unit/AnimalRepositoryTest.php` — 6 pruebas (sin base de datos)

---

### 3. Observer

**Problema resuelto:** Al registrar un peso, el controlador llamaba secuencialmente a `notificarPropietario()`, `actualizarDashboard()`, `recalcularICC()` y `dispararWebhookSenasa()`. Agregar alertas SMS requería abrir el controlador y añadir otra llamada, acoplando el emisor con sus receptores.

**Solución:** `RegistroPesoSubject` mantiene una lista interna de observadores. El controlador solo llama `registrarPeso()` — el subject notifica automáticamente a todos. El método `notificar()` es privado; agregar un cuarto observador (`AlertaSMS`) no requirió modificar el subject ni los observadores existentes.

**Participantes:**

| Clase | Rol | Ruta |
|---|---|---|
| `IRegistroPesoObserver` | Observer (interfaz) | `app/Observers/IRegistroPesoObserver.php` |
| `RegistroPesoSubject` | Subject | `app/Observers/RegistroPesoSubject.php` |
| `NotificadorPropietario` | ConcreteObserver #1 | `app/Observers/Concretos/NotificadorPropietario.php` |
| `RecalculadorICC` | ConcreteObserver #2 | `app/Observers/Concretos/RecalculadorICC.php` |
| `WebhookSenasa` | ConcreteObserver #3 | `app/Observers/Concretos/WebhookSenasa.php` |
| `AlertaSMS` | ConcreteObserver #4 (extensibilidad) | `app/Observers/Concretos/AlertaSMS.php` |

**Rutas API:**

```
POST   /api/registros-peso   → RegistroPesoController@store
```

**Pruebas:** `tests/Unit/RegistroPesoSubjectTest.php` — 6 pruebas con mocks (sin BD)

> Las pruebas usan una subclase `RegistroPesoSubjectTestable` con Reflection para exponer `notificar()` sin alterar el diseño original.

---

### 4. Strategy

**Problema resuelto:** `EstimadorPesoService` tenía una cadena `if-else` que evaluaba el método de estimación (`yolov8`, `regresion`, `tabla`). Agregar un nuevo algoritmo obligaba a abrir y modificar el servicio, mezclando orquestación con lógica de algoritmos.

**Solución:** Cada algoritmo se encapsula en su propia clase (`ConcreteStrategy`). El contexto (`EstimadorPesoService`) recibe la estrategia por constructor y la llama sin saber cuál es. El resultado se devuelve como un `ValueObject` inmutable (`ResultadoEstimacion`) con propiedades `readonly`. Incluye fallback automático: si YOLOv8 no está disponible, el servicio usa `AlgoritmoTablaReferencia` sin intervención del llamador.

**Participantes:**

| Clase | Rol | Ruta |
|---|---|---|
| `IAlgoritmoEstimacion` | Strategy (interfaz) | `app/Strategies/IAlgoritmoEstimacion.php` |
| `AlgoritmoYolov8` | ConcreteStrategy #1 | `app/Strategies/Concretas/AlgoritmoYolov8.php` |
| `AlgoritmoRegresionLineal` | ConcreteStrategy #2 | `app/Strategies/Concretas/AlgoritmoRegresionLineal.php` |
| `AlgoritmoTablaReferencia` | ConcreteStrategy #3 (fallback) | `app/Strategies/Concretas/AlgoritmoTablaReferencia.php` |
| `EstimadorPesoService` | Context (sin if-else) | `app/Services/EstimadorPesoService.php` |
| `ResultadoEstimacion` | Value Object inmutable | `app/Domain/ResultadoEstimacion.php` |
| `EstimadorFactory` | Factory de estrategias | `app/Strategies/EstimadorFactory.php` |

**Rutas API:**

```
POST   /api/estimaciones           → EstimadorController@estimar
POST   /api/estimaciones/fallback  → EstimadorController@estimarConFallback
```

**Pruebas:** `tests/Unit/EstimadorPesoServiceTest.php` — 8 pruebas

---

## Estructura de archivos

```
app/
├── Domain/
│   ├── Raza.php                          # Factory — Product abstracto
│   ├── ResultadoEstimacion.php           # Strategy — Value Object readonly
│   └── Razas/
│       ├── Brahman.php
│       ├── Nelore.php
│       └── Angus.php
├── Factories/
│   ├── IRazaFactory.php
│   └── RazaFactory.php
├── Models/
│   ├── Animal.php
│   └── RegistroPeso.php
├── Observers/
│   ├── IRegistroPesoObserver.php
│   ├── RegistroPesoSubject.php
│   └── Concretos/
│       ├── NotificadorPropietario.php
│       ├── RecalculadorICC.php
│       ├── WebhookSenasa.php
│       └── AlertaSMS.php
├── Repositories/
│   ├── IAnimalRepository.php
│   ├── EloquentAnimalRepository.php
│   └── InMemoryAnimalRepository.php
├── Services/
│   ├── ReporteService.php
│   └── EstimadorPesoService.php
├── Strategies/
│   ├── IAlgoritmoEstimacion.php
│   ├── EstimadorFactory.php
│   └── Concretas/
│       ├── AlgoritmoYolov8.php
│       ├── AlgoritmoRegresionLineal.php
│       └── AlgoritmoTablaReferencia.php
└── Http/Controllers/
    ├── AnimalController.php
    ├── RanchoController.php
    ├── RegistroPesoController.php
    └── EstimadorController.php

tests/Unit/
├── RazaFactoryTest.php            # 7 pruebas
├── AnimalRepositoryTest.php       # 6 pruebas
├── RegistroPesoSubjectTest.php    # 6 pruebas
└── EstimadorPesoServiceTest.php   # 8 pruebas
```

---

## Service Container (AppServiceProvider)

Todos los patrones se registran en `app/Providers/AppServiceProvider.php`:

```php
// Factory — singleton: una sola instancia de RazaFactory
$this->app->singleton(IRazaFactory::class, RazaFactory::class);

// Repository — bind: instancia nueva por request
$this->app->bind(IAnimalRepository::class, EloquentAnimalRepository::class);

// Observer — singleton: subject compartido con los 4 observadores suscritos
$this->app->singleton(RegistroPesoSubject::class, function () { ... });

// Strategy — bind: EstimadorPesoService con YOLOv8 por defecto
$this->app->bind(EstimadorPesoService::class, function () { ... });
$this->app->singleton(EstimadorFactory::class, EstimadorFactory::class);
```

---

## Ejecución

### Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### Pruebas

```bash
# Todas las pruebas
php artisan test --compact tests/Unit/

# Por patrón
php artisan test --compact tests/Unit/RazaFactoryTest.php
php artisan test --compact tests/Unit/AnimalRepositoryTest.php
php artisan test --compact tests/Unit/RegistroPesoSubjectTest.php
php artisan test --compact tests/Unit/EstimadorPesoServiceTest.php
```

**Resultado esperado: 29/29 pruebas en verde**

```
Factory    ████████████████████  7 pruebas ✓
Repository ████████████████████  6 pruebas ✓
Observer   ████████████████████  6 pruebas ✓
Strategy   ████████████████████  8 pruebas ✓
                            Total: 29 ✓
```

### Servidor local

```bash
php artisan serve
```

### Ejemplos con curl

**Factory — Registrar animal Brahman:**
```bash
curl -X POST http://localhost:8000/api/animales \
  -H "Content-Type: application/json" \
  -d '{"nombre_animal":"Toro 01","raza":"brahman","peso_foto_kg":350}'
```

**Repository — Listar animales de un rancho:**
```bash
curl http://localhost:8000/api/ranchos/1/animales
```

**Observer — Registrar peso (notifica los 4 observadores):**
```bash
curl -X POST http://localhost:8000/api/registros-peso \
  -H "Content-Type: application/json" \
  -d '{"animal_id":1,"peso_kg":380.5,"metodo_usado":"yolov8","confianza_porcentaje":87.3}'
```

**Strategy — Estimar peso con regresión lineal:**
```bash
curl -X POST http://localhost:8000/api/estimaciones \
  -H "Content-Type: application/json" \
  -d '{"raza":"nelore","peso_referencia_kg":320,"metodo":"regresion"}'
```

**Strategy — Fallback automático (YOLOv8 caído → tabla):**
```bash
curl -X POST http://localhost:8000/api/estimaciones/fallback \
  -H "Content-Type: application/json" \
  -d '{"raza":"brahman","peso_referencia_kg":350}'
```

---

## Decisiones de diseño notables

**PHPUnit 12 y atributos PHP:** La versión 12 de PHPUnit eliminó la anotación `@test` de docblock. Todas las pruebas usan el atributo `#[Test]` de PHP 8.

**InMemoryAnimalRepository:** Las 6 pruebas del patrón Repository no tocan la base de datos. La interfaz `IAnimalRepository` permite intercambiar la implementación Eloquent por una en memoria sin modificar ningún test.

**`notificar()` privado en el Subject:** El método de notificación es privado por diseño. Para testearlo sin exponer la encapsulación, se usa una subclase de prueba con `ReflectionMethod` que invoca el método privado, dejando el diseño original intacto.

**`ResultadoEstimacion` como Value Object:** Las propiedades son `readonly` de PHP 8.1. El test `resultado_es_inmutable` verifica que intentar asignar a una propiedad lanza `\Error`, confirmando la inmutabilidad en tiempo de ejecución.

**`singleton` vs `bind`:** El Subject del Observer es singleton porque todos los requests deben compartir la misma lista de observadores suscritos. El repositorio usa `bind` para obtener una instancia limpia por request y evitar estado compartido entre peticiones.
