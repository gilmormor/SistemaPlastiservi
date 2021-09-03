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
            $table->string('antideslizanteobs',200)->comment('Antideslizante Observaciones');
            $table->tinyInteger('antiestatico')->comment('antiestatico Si=1, No=0');
            $table->string('antiestaticoobs',200)->comment('antiestaticoobs Observaciones');
            $table->tinyInteger('antiblock')->comment('Antiblock Si=1, No=0');
            $table->string('antiblockobs',200)->comment('Antiblockobs Observaciones');
            $table->tinyInteger('aditivootro')->comment('Aditivos Otros Si=1, No=0');
            $table->string('aditivootroobs',200)->comment('Aditivo otro Observaciones');
            $table->double('ancho',18,2)->comment('Ancho');
            $table->unsignedBigInteger('anchoum_id')->comment('Unidad de Medida Ancho Ejm: Cm.,Mic.');
            $table->foreign('anchoum_id','fk_acuerdotecnicotemp_anchounidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->string('anchodesv',200)->comment('Ancho Desviacion');
            $table->double('largo',18,2)->comment('Largo');
            $table->unsignedBigInteger('largoum_id')->comment('Unidad de medida Largo Ejm: Cm.,Mic.');
            $table->foreign('largoum_id','fk_acuerdotecnicotemp_largounidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->string('largodesv',200)->comment('Ancho Desviacion');

            $table->double('fuelle',18,2)->comment('Fuelle');
            $table->unsignedBigInteger('fuelleum_id')->comment('Unidad de medida Largo Ejm: Cm.,Mic.');
            $table->foreign('fuelleum_id','fk_acuerdotecnicotemp_fuelleunidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->string('fuelledesv',200)->comment('Ancho Desviacion');


            $table->double('espesor',18,2)->comment('Espesor');
            $table->unsignedBigInteger('espesorum_id')->comment('Unidad de medida Largo Ejm: Cm.,Mic.');
            $table->foreign('espesorum_id','fk_acuerdotecnicotemp_espesorunidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->string('espesordesv',200)->comment('Ancho Desviacion');

            $table->string('npantone')->comment('Codigo de color de la bolsa. Esto viene de un talon de colores');
            $table->tinyInteger('translucidez')->comment('1=No translucido, 2=Opaco semi translucido, 3=Alta Transparencia');
            $table->tinyInteger('impreso')->comment('Producto impreso 1=Si, 0=No');
            $table->string('impresofoto',200)->comment('Foto del arte impreso en la bolsa, nombre de archivo.');
            $table->string('impresocolor',100)->comment('Color tinta de Impresiono.');
            $table->string('impresoobs',200)->comment('Impreso Observaciones.');
            $table->tinyInteger('sfondo')->comment('Sellado: Fondo 1=si, 0=no');
            $table->string('sfondoobs',200)->comment('Sellado: Fondo Observaciones.');
            $table->tinyInteger('slateral')->comment('Sellado: Lateral 1=si, 0=no');
            $table->string('slateralobs',200)->comment('Sellado: lateral Observaciones.');
            $table->tinyInteger('sprepicado')->comment('Sellado: prepicado 1=si, 0=no');
            $table->string('sprepicadoobs',200)->comment('Sellado: prepicado Observaciones.');
            $table->tinyInteger('slamina')->comment('Sellado: Lamina 1=si, 0=no');
            $table->string('slaminaobs',200)->comment('Sellado: Lamina Observaciones.');
            $table->tinyInteger('sfunda')->comment('Sellado: Funda 1=si, 0=no');
            $table->string('sfundaobs',200)->comment('Sellado: funda Observacion.');
            $table->string('feunidxpaq',200)->comment('Forma de empaque: Unidades por empaque');
            $table->string('feunidxpaqobs',200)->comment('Forma de empaque: Unidades por empaque Observacion.');
            $table->string('feunidxcont',200)->comment('Forma de embalaje: Unidades por contenedor');
            $table->string('feunidxcontobs',200)->comment('Forma de embalaje: Unidades por contenedor Observacion');
            $table->string('fecolorcont',200)->comment('Forma de embalaje: Color contenedor.');
            $table->string('fecolorcontobs',200)->comment('Forma de embalaje: Color contenedor observaciones.');
            $table->string('feunitxpalet',200)->comment('Forma de embalaje: Unidades por palet.');
            $table->string('feunitxpaletobs',200)->comment('Forma de embalaje: Unidades por palet Observaciones.');
            $table->tinyInteger('etiqplastiservi')->comment('Etiquetado: Plastiservi 1=Si, 0=No.');
            $table->string('etiqplastiserviobs',200)->comment('Etiquetado: Plastiservi Observaciones.');
            $table->string('etiqotro',200)->comment('Etiquetado: Otro.');
            $table->string('etiqotroobs',200)->comment('Etiquetado: Otro Observaciones.');
            $table->string('otrocertificado',200)->comment('Otros certificados: Especifique.');

            $table->unsignedBigInteger('color_id')->comment('Color de producto acuerdo tecnico.')->nullable();
            $table->foreign('color_id','fk_acuerdotecnicotemp_color')->references('id')->on('color')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('impresocolor_id')->comment('Color de impreso en producto acuerdo tecnico.')->nullable();
            $table->foreign('impresocolor_id','fk_acuerdotecnicotempimpreso_color')->references('id')->on('color')->onDelete('restrict')->onUpdate('restrict');

            $table->unsignedBigInteger('materiaprima_id')->comment('Codigo materia prima.')->nullable();
            $table->foreign('materiaprima_id','fk_acuerdotecnicotemp_materiaprima')->references('id')->on('materiaprima')->onDelete('restrict')->onUpdate('restrict');

            $table->string('materiaprima_desc',100)->comment('Descripcion de materia prima');
            
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
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
