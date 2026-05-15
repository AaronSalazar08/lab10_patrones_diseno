<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registros_peso', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('animal_id')->comment('ID del animal pesado');
            $table->float('peso_kg')->comment('Peso estimado en kilogramos');
            $table->float('confianza_porcentaje')->default(0)->comment('Confianza del algoritmo');
            $table->string('metodo_usado', 50)->comment('yolov8, regresion, tabla');
            $table->string('foto_path', 255)->nullable()->comment('Ruta de la fotografía');
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamps();

            $table->foreign('animal_id')->references('id')->on('animales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_peso');
    }
};
