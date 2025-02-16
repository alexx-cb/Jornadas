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
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('ponente_id')->constrained();
            $table->enum('tipo_evento', ['Taller', 'Conferencia']);
            $table->string('descripcion');
            $table->enum('dia', ['Jueves', 'Viernes']);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->integer('cupo_maximo');
            $table->integer('cupo_actual');
            $table->timestamps();

            $table->unique(['ponente_id', 'dia', 'hora_inicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
