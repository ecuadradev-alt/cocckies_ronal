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
        Schema::create('news', function (Blueprint $table) {
            $table->id();

            // 🔹 Campos del modelo News
            $table->string('titulo');                     // título de la noticia
            $table->text('descripcion');                  // descripción o contenido
            $table->string('url', 500)->nullable();       // enlace a la noticia original
            $table->date('fecha_publicacion')->nullable();// fecha de publicación

            // 🔹 Campos opcionales adicionales (si quieres expandir más adelante)
            // $table->string('autor')->nullable();
            // $table->string('categoria')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
