<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAcuerdotecnicotemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acuerdotecnicotemp', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('desc',100)->comment('Descripcion del producto');
            $table->tinyInteger('entmuestra')->comment('No=, Si=1')->default(0);
            $table->string('materiaprimaobs',200)->comment('Observacion Materia prima');
            $table->string('usoprevisto',200)->comment('Uso previsto. Ejemplo: Para contacto con alimentos');
            $table->tinyInteger('uv')->comment('UV Si=1, No=0')->default(0);
            $table->string('uvobs',200)->comment('UV Observaciones');
            $table->tinyInteger('antideslizante')->comment('Antideslizante Si=1, No=0');
            $table->string('antideslizanteobs')->comment('Antideslizante Observaciones');
            $table->tinyInteger('antiestatico')->comment('antiestatico Si=1, No=0');
            $table->string('antiestaticoobs')->comment('antiestaticoobs Observaciones');
            $table->tinyInteger('antiblock')->comment('Antiblock Si=1, No=0');
            $table->string('antiblockobs')->comment('Antiblockobs Observaciones');
            $table->tinyInteger('aditivootro')->comment('Aditivos Otros Si=1, No=0');
            $table->string('aditivootroobs')->comment('Aditivo otro Observaciones');
            $table->double('ancho',18,2)->comment('Ancho');
            $table->unsignedBigInteger('anchoum_id')->comment('Unidad de Medida Ancho Ejm: Cm.,Mic.');
            $table->foreign('anchoum_id','fk_acuerdotecnicotemp_anchounidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->string('anchodesv')->comment('Ancho Desviacion');
            $table->double('largo',18,2)->comment('Largo');
            $table->unsignedBigInteger('largoum_id')->comment('Unidad de medida Largo Ejm: Cm.,Mic.');
            $table->foreign('largoum_id','fk_acuerdotecnicotemp_largounidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->string('largodesv')->comment('Ancho Desviacion');

            $table->double('fuelle',18,2)->comment('Fuelle');
            $table->unsignedBigInteger('fuelleum_id')->comment('Unidad de medida Largo Ejm: Cm.,Mic.');
            $table->foreign('fuelleum_id','fk_acuerdotecnicotemp_fuelleunidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->string('fuelledesv')->comment('Ancho Desviacion');


            $table->double('espesor',18,2)->comment('Espesor');
            $table->unsignedBigInteger('fuelleum_id')->comment('Unidad de medida Largo Ejm: Cm.,Mic.');
            $table->foreign('espesorum_id','fk_acuerdotecnicotemp_espesorunidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->string('espesordesv')->comment('Ancho Desviacion');

            
            $table->softDeletes();
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acuerdotecnicotemp');
    }
}
