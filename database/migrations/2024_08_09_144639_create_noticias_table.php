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
        Schema::create('noticias', function (Blueprint $table) {

            $table->id();

            $table->string('titulo', 250); // El título de la noticia.
            $table->text('contenido'); // Aquí se guarda el texto completo de la noticia. Se utiliza TEXT para permitir contenido largo.
            $table->dateTime('fecha_publicacion');
            $table->string('autor', 250);
            $table->string('imagen', 250);

            $table->unsignedBigInteger('seccion_id'); // Define columna de clave foránea
            // Crear la relación de clave foránea
            $table->foreign('seccion_id')->references('id')->on('seccion_noticias')->onUpdate('cascade')->onDelete('restrict');

            $table->text('resumen'); // Resumen o extracto de la noticia para mostrar en listados.

            $table->integer('estado')->default(1); // Estado de la noticia (1 = activo, 0 = inactivo).
            $table->integer('visitas'); // Numero de visitas de la noticia.

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
        Schema::dropIfExists('noticias');
    }
};
