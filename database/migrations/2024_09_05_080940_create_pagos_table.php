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
            $table->unsignedBigInteger('factura_id'); // Define columna de clave foránea
            $table->foreign('factura_id')->references('id')->on('facturas')->onUpdate('cascade')->onDelete('restrict');

            // Quie registro el pago
            $table->unsignedBigInteger('usuario_id'); // Define columna de clave foránea
            $table->foreign('usuario_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');

            // Crear la relación de clave foránea
            $table->unsignedBigInteger('multa_id')->nullable(); // Define columna de clave foránea
            $table->foreign('multa_id')->references('id')->on('multas')->onUpdate('cascade')->onDelete('restrict');

            // Crear la relación de clave foránea
            // Relación con la tabla puestos
            $table->unsignedBigInteger('puesto_id'); // Define columna de clave foránea
            $table->foreign('puesto_id')->references('id')->on('puestos')->onUpdate('cascade')->onDelete('restrict');

            $table->double('monto_pago');
            $table->dateTime('fecha_pago');
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
