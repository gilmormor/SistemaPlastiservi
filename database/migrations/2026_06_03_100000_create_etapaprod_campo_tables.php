<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Campos adicionales por etapa de producción.
 *
 * etapaprod_campo          — define qué campos extra tiene cada apsucetapaprod
 * opdetregprodtemp_campoval — valores ingresados por el operario (antes de aprobar)
 * opdetregprod_campoval     — valores copiados al aprobar (registro definitivo)
 *
 * Estas tablas son 100% aditivas: no modifican ninguna tabla existente.
 * Si una etapa no tiene campos definidos, el flujo actual no cambia en absoluto.
 */
class CreateEtapaprodCampoTables extends Migration
{
    public function up()
    {
        // 1. Definición de campos extra por etapa
        Schema::create('etapaprod_campo', function (Blueprint $table) {
            $table->bigIncrements('id');
            // A qué configuración de etapa por área-sucursal pertenece
            $table->unsignedBigInteger('apsucetapaprod_id');
            // Nombre interno (usado en fórmulas de campos calculados)
            $table->string('nombre', 50);
            // Etiqueta que ve el usuario
            $table->string('etiqueta', 100);
            // Tipo: number | text | calculated
            $table->enum('tipo', ['number', 'text', 'calculated'])->default('number');
            // Fórmula JS solo para tipo=calculated. Usa nombres de otros campos.
            // Ej: "cantidad_sacos * unidades_por_saco"
            $table->string('formula', 300)->nullable();
            // Unidad de medida visual (kg, un, etc.)
            $table->string('unidad', 20)->nullable();
            // Decimales para tipo=number y tipo=calculated
            $table->tinyInteger('decimales')->default(2);
            // Si el campo es obligatorio
            $table->boolean('requerido')->default(false);
            // Orden de aparición en pantalla
            $table->tinyInteger('orden')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('apsucetapaprod_id')
                  ->references('id')->on('areaproduccionsucetapaprod')
                  ->onDelete('cascade');

            // Un campo tiene nombre único dentro de la misma etapa
            $table->unique(['apsucetapaprod_id', 'nombre']);
        });

        // 2. Valores ingresados por el operario (registro temporal)
        Schema::create('opdetregprodtemp_campoval', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdetregprodtemp_id');
            $table->unsignedBigInteger('etapaprod_campo_id');
            // Todos los valores se almacenan como string; se castean según tipo al mostrar
            $table->string('valor', 500)->nullable();
            $table->timestamps();

            $table->foreign('opdetregprodtemp_id')
                  ->references('id')->on('opdetregprodtemp')
                  ->onDelete('cascade');
            $table->foreign('etapaprod_campo_id')
                  ->references('id')->on('etapaprod_campo')
                  ->onDelete('cascade');

            // Un campo aparece una sola vez por registro
            $table->unique(['opdetregprodtemp_id', 'etapaprod_campo_id'], 'uq_temp_campo');
        });

        // 3. Valores en el registro aprobado (espejo definitivo)
        Schema::create('opdetregprod_campoval', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdetregprod_id');
            $table->unsignedBigInteger('etapaprod_campo_id');
            $table->string('valor', 500)->nullable();
            $table->timestamps();

            $table->foreign('opdetregprod_id')
                  ->references('id')->on('opdetregprod')
                  ->onDelete('cascade');
            $table->foreign('etapaprod_campo_id')
                  ->references('id')->on('etapaprod_campo')
                  ->onDelete('cascade');

            $table->unique(['opdetregprod_id', 'etapaprod_campo_id'], 'uq_prod_campo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('opdetregprod_campoval');
        Schema::dropIfExists('opdetregprodtemp_campoval');
        Schema::dropIfExists('etapaprod_campo');
    }
}
