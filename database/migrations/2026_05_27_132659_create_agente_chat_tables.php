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
        // 1. Tabla de Fases de Precio (Conocimiento Dinámico)
        Schema::create('fases_precio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_fase');
            $table->decimal('precio_unitario', 8, 2);
            $table->decimal('precio_promocional', 8, 2);
            $table->boolean('activa')->default(false);
            $table->timestamps();
        });

        // 2. Tabla de Sesiones de Chat (Memoria Principal)
        Schema::create('sesiones_chat', function (Blueprint $table) {
            $table->id();
            // Lo vinculamos a la tabla 'users' que Laravel trae por defecto
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('estado')->default('Activo'); // Activo, Inscrito, Cerrado
            $table->timestamps();
        });

        // 3. Tabla de Mensajes (Historial Conversacional)
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_id')->constrained('sesiones_chat')->onDelete('cascade');
            $table->enum('remitente', ['usuario', 'agente']);
            $table->text('contenido');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes');
        Schema::dropIfExists('sesiones_chat');
        Schema::dropIfExists('fases_precio');
    }
};