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
        Schema::create('periodo_pagados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pago_id'); // Relación con pagos
            $table->foreign('pago_id')->references('id')->on('pagos')->onDelete('cascade');

            $table->date('periodo'); // Mes de pago o adeudo
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
        Schema::dropIfExists('periodo_pagados');
    }
};
