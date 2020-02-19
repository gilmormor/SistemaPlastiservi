<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablaAcuerdotectemp extends Migration
{
    /**
     * Run the migrations.
     * Acuerdo tecnico Temporal
     * @return void
     */
    public function up()
    {
        Schema::create('acuerdotectemp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombreprod',100)->comment("Nombre del Producto");
            $table->integer('entmuestra')->comment("Entrega de Muestra 1=si, 0=no");
            $table->string('matfrabobs',60)->comment("Observacion Material de Fabricación");
            $table->string('usoprevisto',100)->comment("Uso previsto. Ejemplo: Para contacto con alimentos");
            $table->integer('uv')->comment("UV 1=si, 0=no");
            $table->string('uvobs',100)->comment("UV Observaciones");
            $table->integer('antideslizante')->comment("Antideslizante 1=si, 0=no");
            $table->string('antideslizanteobs',100)->comment("Antideslizante Observaciones");
            $table->integer('antiestatico')->comment("Antiestatico 1=si, 0=no");
            $table->string('antiestaticoobs',100)->comment("Antiestatico Observaciones");
            $table->integer('antiblock')->comment("Antiblock 1=si, 0=no");
            $table->string('antiblockobs',100)->comment("Antiblock Observaciones");
            $table->integer('aditivootro')->comment("Aditivos Otros 1=si, 0=no");
            $table->string('aditivootroobs',100)->comment("Aditivo otro Observaciones");
            $table->float('ancho',8,2)->comment("Ancho");
            $table->unsignedBigInteger('anchoum_id')->comment("Unidad de Medida Ancho Ejm: Cm.,Mic.");
            $table->foreign('anchoum_id','fk_acuerdotectemp_anchoum')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');

            $table->string('anchodesv',100)->nullable()->comment("Ancho Desviacion");
            $table->float('largo',8,2)->comment("Largo");
            $table->unsignedBigInteger('largoum_id')->comment("Unidad de medida Largo Ejm: Cm.,Mic.");
            $table->foreign('largoum_id','fk_acuerdotectemp_largoum')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');

            $table->string('largodesv',100)->nullable()->comment("Ancho Desviacion");
            $table->float('fuelle')->comment("Fuelle");
            $table->unsignedBigInteger('fuelleum_id')->comment("Unidad de medida fuelle Ejm: Cm.,Mic.");
            $table->foreign('fuelleum_id','fk_acuerdotectemp_fuelleum')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');

            $table->string('fuelledesv',100)->nullable()->comment("Fuelle Desviacion");
            $table->float('espesor',8,2)->comment("Espesor");
            $table->unsignedBigInteger('espesorum_id')->comment("Espesor Unidad de Medida");
            $table->foreign('espesorum_id','fk_acuerdotectemp_espesorum')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');

            $table->string('espesordesv',100)->nullable()->comment("Espesor Desviacion");
            $table->string('npantone',50)->comment("Codigo de color de la bolsa. Esto viene de un talon de colores");
            $table->integer('translucidez')->comment("1=No translucido, 2=Opaco semi translucido, 3=Alta Transparencia");
            $table->integer('impreso')->comment("Producto impreso 1=Si, 0=No");
            $table->string('impresofoto',100)->nullable()->comment("Foto del arte impreso en la bolsa");
            $table->string('impresocolor',45)->nullable()->comment("Color tinta de Impresion");
            $table->string('impresoobs',100)->nullable()->comment("Impreso Observaciones");
            $table->integer('sfondo')->comment("Sellado: Fondo 1=si, 0=no");
            $table->string('sfondoobs',100)->nullable()->comment("Sellado: Fondo Observaciones");
            $table->integer('slateral')->comment("Sellado: Lateral 1=si, 0=no");
            $table->string('slateralobs',100)->nullable()->comment("Sellado: lateral Observaciones");
            $table->integer('sprepicado')->comment("Sellado: prepicado 1=si, 0=no");
            $table->string('sprepicadoobs',100)->nullable()->comment("Sellado: prepicado Observaciones");
            $table->integer('slamina')->comment("Sellado: Lamina 1=si, 0=no");
            $table->string('slaminaobs',100)->nullable()->comment("Sellado: Lamina Observaciones");
            $table->integer('sfunda')->comment("Sellado: Funda 1=si, 0=no");
            $table->string('sfundaobs',100)->nullable()->comment("Sellado: funda Observacion");
            $table->string('feunidxpaq',100)->nullable()->comment("Forma de empaque: Unidades por empaque");
            $table->string('feunidxpaqobs',100)->nullable()->comment("Forma de empaque: Unidades por empaque Observacion");
            $table->string('feunidxcont',100)->nullable()->comment("Forma de embalaje: Unidades por contenedor");
            $table->string('feunidxcontobs',100)->nullable()->comment("Forma de embalaje: Unidades por contenedor Observacion");
            $table->string('fecolorcont',45)->nullable()->comment("Forma de embalaje: Color contenedor");
            $table->string('fecolorcontobs',100)->nullable()->comment("Forma de embalaje: Color contenedor observaciones");
            $table->string('feunitxpalet',100)->nullable()->comment("Forma de embalaje: Unidades por palet");
            $table->string('feunitxpaletobs',100)->nullable()->comment("Forma de embalaje: Unidades por palet Observaciones");
            $table->string('etiqplastiservi',100)->comment("Etiquetado: Plastiservi 1=Si, 0=No");
            $table->string('etiqplastiserviobs',100)->comment("Etiquetado: Plastiservi Observaciones");
            $table->string('etiqotro',100)->comment("Etiquetado: Otro");
            $table->string('etiqotroobs',100)->comment("Etiquetado: Otro Observaciones");
            $table->string('otrocertificado',100)->comment("Otros certificados: Especifique");
            $table->string('despacharA',100)->comment("Despachar A");
            $table->string('nomcli',100)->comment("Nombre del Cliente");
            $table->date('fechacuerdocli')->comment("Fecha Acuerdo con el cliente");
            $table->unsignedBigInteger('categoriaprod_id')->comment("Codigo de categoria Producto");
            $table->foreign('categoriaprod_id','fk_acuerdotectemp_categoriaprod')->references('id')->on('categoriaprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('color_id');
            $table->foreign('color_id','fk_acuerdotectemp_color')->references('id')->on('color')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('impresocolor_id')->comment('Codigo Color tinta de Impresion');
            $table->foreign('impresocolor_id','FK_acuerdotectemp_colorImp')->references('id')->on('color')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('formapago_id');
            $table->foreign('formapago_id','fk_acuerdotectemp_formapago')->references('id')->on('formapago')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('plazopago_id');
            $table->foreign('plazopago_id','fk_acuerdotectemp_plazopago')->references('id')->on('plazopago')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('vendedor_id');
            $table->foreign('vendedor_id','fk_acuerdotectemp_vendedor')->references('id')->on('vendedor')->onDelete('restrict')->onUpdate('restrict');
/*
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id','fk_acuerdotectemp_cliente')->references('id')->on('cliente')->onDelete('restrict')->onUpdate('restrict');
*/
            $table->unsignedBigInteger('clientedirec_id');
            $table->foreign('clientedirec_id','fk_acuerdotectemp_clientedirec')->references('id')->on('clientedirec')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('matfabr_id');
            $table->foreign('matfabr_id','fk_acuerdotectemp_matfabr')->references('id')->on('matfabr')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acuerdotectemp');
    }
}
