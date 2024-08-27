<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            // Crear la relación de clave foránea
            $table->unsignedBigInteger('puesto_id'); // Define columna de clave foránea
            $table->foreign('puesto_id')->references('id')->on('puestos')->onUpdate('cascade')->onDelete('restrict');

            // Quie registro el pago
            $table->unsignedBigInteger('usuario_id'); // Define columna de clave foránea
            $table->foreign('usuario_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');

            $table->double('monto_pagado');
            $table->dateTime('fecha_pagado');
            $table->date('periodo');

            $table->boolean('multa'); // Booleano o entero, si hubo multa en este pago
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos');
    }
};
