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
        Schema::create('generars', function (Blueprint $table) {
            $table->id();

            $table->date('fecha_generacion_cobro'); // Mes de pago o adeudo

            // Relación con la tabla puestos (u otros productos)
            $table->unsignedBigInteger('usuario_id')->nullable(true); // Define columna de clave foránea
            $table->foreign('usuario_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::dropIfExists('generars');
    }
};
