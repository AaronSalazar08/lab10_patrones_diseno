<?php

namespace App\Observers\Concretos;

use App\Models\RegistroPeso;
use App\Observers\IRegistroPesoObserver;
use Illuminate\Support\Facades\Log;

class WebhookSenasa implements IRegistroPesoObserver
{
    private string $urlWebhook;

    public function __construct(string $urlWebhook = 'https://api.senasa.go.cr/webhook/peso')
    {
        $this->urlWebhook = $urlWebhook;
    }

    public function onPesoRegistrado(RegistroPeso $registro): void
    {
        // En producción haría la llamada HTTP real a SENASA.
        // Se usa try/catch para que un fallo del webhook no rompa el flujo.
        try {
            // Http::post($this->urlWebhook, [
            //     'animal_id' => $registro->animal_id,
            //     'peso_kg'   => $registro->peso_kg,
            //     'fecha'     => $registro->fecha_registro,
            // ]);

            Log::info('[WebhookSenasa] Notificación enviada a SENASA para animal ID: '
                .$registro->animal_id
                .' | Peso: '.$registro->peso_kg.' kg'
            );
        } catch (\Exception $e) {
            Log::error('[WebhookSenasa] Error al notificar a SENASA: '.$e->getMessage());
        }
    }
}
