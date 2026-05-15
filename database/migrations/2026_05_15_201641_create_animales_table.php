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
        Schema::create('animales', function (Blueprint $table) {
            $table->id();
            $table->string('arete', 20)->unique()->comment('Número de arete SENASA');
            $table->string('nombre', 100)->nullable()->comment('Nombre opcional del animal');
            $table->string('raza', 50)->comment('Raza del animal: brahman, nelore, angus');
            $table->string('sexo', 10)->default('macho')->comment('macho o hembra');
            $table->enum('estado', ['activo', 'vendido', 'muerto'])->default('activo');
            $table->unsignedBigInteger('rancho_id')->comment('ID del rancho al que pertenece');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animales');
    }
};
