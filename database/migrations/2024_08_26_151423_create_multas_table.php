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
        Schema::create('multas', function (Blueprint $table) {

            $table->id();
            // Crear la relación de clave foránea
            $table->unsignedBigInteger('puesto_id'); // Define columna de clave foránea
            $table->foreign('puesto_id')->references('id')->on('puestos')->onUpdate('cascade')->onDelete('restrict');
            $table->double('monto_multa');
            $table->dateTime('fecha_generacion');
            $table->string('periodos_afectados', 1500);
            $table->string('estado_multa'); /* Ej: 'pendiente', 'pagada' */
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
        Schema::dropIfExists('multas');
    }
};
