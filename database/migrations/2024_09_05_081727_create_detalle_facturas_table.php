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
        Schema::create('detalle_facturas', function (Blueprint $table) {

            // Definir 'num_detalle' sin auto-increment
            $table->unsignedBigInteger('num_detalle'); // Campo 1 de la PK compuesta
            $table->unsignedBigInteger('factura_id');  // Campo 2 de la PK compuesta

            // Crear la clave primaria compuesta
            $table->primary(['num_detalle', 'factura_id']); // PK compuesta

            // Relación con la tabla facturas
            $table->foreign('factura_id')->references('id')->on('facturas')->onUpdate('cascade')->onDelete('restrict');

            // Relación con la tabla puestos
            $table->unsignedBigInteger('puesto_id'); // Define columna de clave foránea
            $table->foreign('puesto_id')->references('id')->on('puestos')->onUpdate('cascade')->onDelete('restrict');

            $table->string('periodo', 1500); // Periodos a cobrar
            $table->double('precio');

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
        Schema::dropIfExists('detalle_facturas');
    }
};
