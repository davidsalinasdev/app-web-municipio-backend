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
        Schema::create('titulars', function (Blueprint $table) {

            $table->id();
            $table->string('nombres', 100);
            $table->string('apellidos', 80);
            $table->string('carnet', 15)->unique();
            // Crear la relación de clave foránea
            $table->unsignedBigInteger('usuario_id'); // Define columna de clave foránea
            $table->foreign('usuario_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->integer('estado')->default(1);
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
        Schema::dropIfExists('titulars');
    }
};
