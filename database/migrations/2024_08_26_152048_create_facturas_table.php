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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('nro_recibo', 250)->nullable(true); // Número de recibo.
            $table->date('fecha_emision')->nullable(true);

            // Relación con la tabla puestos (u otros productos)
            $table->unsignedBigInteger('usuario_id')->nullable(true); // Define columna de clave foránea
            $table->foreign('usuario_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');

            // $table->date('fecha_vencimiento');
            // $table->double('total');
            // $table->text('observaciones');
            $table->integer('estado_pago')->default(0);
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
        Schema::dropIfExists('facturas');
    }
};
