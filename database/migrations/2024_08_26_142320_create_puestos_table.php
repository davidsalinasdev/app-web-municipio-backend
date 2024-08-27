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
        Schema::create('puestos', function (Blueprint $table) {

            $table->id();
            $table->string('nro_puesto', 250);
            // Crear la relación de clave foránea
            $table->unsignedBigInteger('sector_id'); // Define columna de clave foránea
            $table->foreign('sector_id')->references('id')->on('sectors')->onUpdate('cascade')->onDelete('restrict');

            // Crear la relación de clave foránea
            $table->unsignedBigInteger('titular_id'); // Define columna de clave foránea
            $table->foreign('titular_id')->references('id')->on('titulars')->onUpdate('cascade')->onDelete('restrict');

            // Crear la relación de clave foránea
            $table->unsignedBigInteger('usuario_id'); // Define columna de clave foránea
            $table->foreign('usuario_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');

            $table->double('precio_mensual');
            $table->string('nro_contrato', 250);
            $table->dateTime('fecha_ingreso');
            $table->text('observaciones'); // Se utiliza TEXT para permitir contenido largo.
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
        Schema::dropIfExists('puestos');
    }
};
