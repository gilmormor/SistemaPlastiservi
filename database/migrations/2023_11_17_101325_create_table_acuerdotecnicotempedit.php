<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAcuerdotecnicotempedit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acuerdotecnicotempedit', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('acuerdotecnicotemp_id')->nullable();
            $table->foreign('acuerdotecnicotemp_id','fk_acuerdotecnicotempedit_acuerdotecnicotemp')->references('id')->on('acuerdotecnicotemp')->onDelete('restrict')->onUpdate('restrict');
            $table->tinyInteger('at_status')->comment('1=Registro Original, 2=Nuevo Registro editado.');
            $table->unsignedBigInteger('at_cotizaciondetalle_id')->nullable();
            $table->foreign('at_cotizaciondetalle_id','fk_acuerdotecnicotempedit_cotizaciondetalle')->references('id')->on('cotizaciondetalle')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('at_claseprod_id')->nullable();
            $table->foreign('at_claseprod_id','fk_acuerdotecnicotempedit_claseprod')->references('id')->on('claseprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('at_grupoprod_id')->nullable();
            $table->foreign('at_grupoprod_id','fk_acuerdotecnicotempedit_grupoprod')->references('id')->on('grupoprod')->onDelete('restrict')->onUpdate('restrict');
            $table->string('at_desc',100)->comment('Descripcion del producto');
            $table->string('at_complementonomprod',20)->nullable()->comment('Complemento nombre de producto para la factura.');
            $table->tinyInteger('at_entmuestra')->comment('No=, Si=1')->default(0);
            $table->unsignedBigInteger('at_color_id')->comment('Color de producto acuerdo tecnico.')->nullable();
            $table->foreign('at_color_id','fk_acuerdotecnicotempedit_color')->references('id')->on('color')->onDelete('restrict')->onUpdate('restrict');
            $table->string('at_npantone',50)->comment('Codigo de color de la bolsa. Esto viene de un talon de colores')->nullable();
            $table->tinyInteger('at_translucidez')->comment('1=No translucido, 2=Opaco semi translucido, 3=Alta Transparencia');
            $table->unsignedBigInteger('at_materiaprima_id')->comment('Codigo materia prima.')->nullable();
            $table->foreign('at_materiaprima_id','fk_acuerdotecnicotempedit_materiaprima')->references('id')->on('materiaprima')->onDelete('restrict')->onUpdate('restrict');
            $table->string('at_materiaprimaobs',200)->comment('Observacion Materia prima')->nullable();
            $table->string('at_usoprevisto',200)->comment('Uso previsto. Ejemplo: Para contacto con alimentos')->nullable();
            $table->tinyInteger('at_pigmentacion')->nullable()->comment('% Pigmentacion materia prima.')->default(0);
            $table->tinyInteger('at_uv')->comment('UV Si=1, No=0')->default(0);
            $table->string('at_uvobs',200)->comment('UV Observaciones')->nullable();
            $table->tinyInteger('at_antideslizante')->comment('Antideslizante Si=1, No=0');
            $table->string('at_antideslizanteobs',200)->comment('Antideslizante Observaciones')->nullable();
            $table->tinyInteger('at_antiestatico')->comment('antiestatico Si=1, No=0');
            $table->string('at_antiestaticoobs',200)->comment('antiestaticoobs Observaciones')->nullable();
            $table->tinyInteger('at_antiblock')->comment('Antiblock Si=1, No=0');
            $table->string('at_antiblockobs',200)->comment('Antiblockobs Observaciones')->nullable();
            $table->tinyInteger('at_aditivootro')->comment('Aditivos Otros Si=1, No=0');
            $table->string('at_aditivootroobs',200)->comment('Aditivo otro Observaciones')->nullable();
            $table->double('at_ancho',6,3)->comment('Ancho');
            $table->unsignedBigInteger('at_anchoum_id')->comment('Unidad de Medida Ancho Ejm: Cm.,Mic.');
            $table->foreign('at_anchoum_id','fk_acuerdotecnicotempedit_anchounidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->string('at_anchodesv',200)->comment('Ancho Desviacion');
            $table->double('at_largo',6,3)->comment('Largo')->nullable();
            $table->unsignedBigInteger('at_largoum_id')->comment('Unidad de medida Largo Ejm: Cm.,Mic.')->nullable();
            $table->foreign('at_largoum_id','fk_acuerdotecnicotempedit_largounidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->string('at_largodesv',200)->comment('Ancho Desviacion')->nullable();
            $table->double('at_fuelle',6,3)->comment('Fuelle')->nullable();
            $table->unsignedBigInteger('at_fuelleum_id')->comment('Unidad de medida Largo Ejm: Cm.,Mic.')->nullable();
            $table->foreign('at_fuelleum_id','fk_acuerdotecnicotempedit_fuelleunidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->string('at_fuelledesv',200)->comment('Ancho Desviacion')->nullable();
            $table->double('at_espesor',4,3)->comment('Espesor');
            $table->unsignedBigInteger('at_espesorum_id')->comment('Unidad de medida Largo Ejm: Cm.,Mic.');
            $table->foreign('at_espesorum_id','fk_acuerdotecnicotempedit_espesorunidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->string('at_espesordesv',200)->comment('Ancho Desviacion');
            $table->unsignedBigInteger('at_unidadmedida_id')->nullable()->comment('Codigo unidad de medida.');
            $table->foreign('at_unidadmedida_id','fk_acuerdotecnicotempedit_unidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->tinyInteger('at_impreso')->comment('Producto impreso 1=Si, 0=No');
            $table->string('at_impresofoto',200)->comment('Foto del arte impreso en la bolsa, nombre de archivo.')->nullable();
            $table->string('at_impresocolor',100)->comment('Color tinta de Impresion.')->nullable();
            $table->string('at_impresoobs',200)->comment('Impreso Observaciones.')->nullable();
            $table->unsignedBigInteger('at_tiposello_id')->comment('Tipo de sellado')->default(1);
            $table->foreign('at_tiposello_id','fk_acuerdotecnicotempedit_tiposello')->references('id')->on('tiposello')->onDelete('restrict')->onUpdate('restrict');
            $table->string('at_tiposelloobs',200)->nullable()->comment('Observacion tipo sello')->default("");
            $table->tinyInteger('at_sfondo')->comment('Sellado: Fondo 1=si, 0=no');
            $table->string('at_sfondoobs',200)->comment('Sellado: Fondo Observaciones.')->nullable();
            $table->tinyInteger('at_slateral')->comment('Sellado: Lateral 1=si, 0=no');
            $table->string('at_slateralobs',200)->comment('Sellado: lateral Observaciones.')->nullable();
            $table->tinyInteger('at_sprepicado')->comment('Sellado: prepicado 1=si, 0=no');
            $table->string('at_sprepicadoobs',200)->comment('Sellado: prepicado Observaciones.')->nullable();
            $table->tinyInteger('at_slamina')->comment('Sellado: Lamina 1=si, 0=no');
            $table->string('at_slaminaobs',200)->comment('Sellado: Lamina Observaciones.')->nullable();
            $table->tinyInteger('at_sfunda')->comment('Sellado: Funda 1=si, 0=no');
            $table->string('at_sfundaobs',200)->comment('Sellado: funda Observacion.')->nullable();
            $table->tinyInteger('at_embalajeplastservi')->comment('Embalaje Plastiservi? 1=Si, 0=No')->nullable()->default("0");
            $table->string('at_feunidxpaq',200)->comment('Forma de empaque: Unidades por empaque')->nullable();
            $table->string('at_feunidxpaqobs',200)->comment('Forma de empaque: Unidades por empaque Observacion.')->nullable();
            $table->string('at_feunidxcont',200)->comment('Forma de embalaje: Unidades por contenedor')->nullable();
            $table->string('at_feunidxcontobs',200)->comment('Forma de embalaje: Unidades por contenedor Observacion')->nullable();
            $table->string('at_fecolorcont',200)->comment('Forma de embalaje: Color contenedor.')->nullable();
            $table->string('at_fecolorcontobs',200)->comment('Forma de embalaje: Color contenedor observaciones.')->nullable();
            $table->string('at_feunitxpalet',200)->comment('Forma de embalaje: Unidades por palet.')->nullable();
            $table->string('at_feunitxpaletobs',200)->comment('Forma de embalaje: Unidades por palet Observaciones.')->nullable();
            $table->tinyInteger('at_etiqplastiservi')->comment('Etiquetado: Plastiservi 1=Si, 0=No.');
            $table->string('at_etiqplastiserviobs',200)->comment('Etiquetado: Plastiservi Observaciones.')->nullable();
            $table->string('at_etiqotro',200)->comment('Etiquetado: Otro.')->nullable();
            $table->string('at_etiqotroobs',200)->comment('Etiquetado: Otro Observaciones.')->nullable();
            $table->string('at_certificados',50)->comment('Certificados. Codigos de certificados.')->nullable();
            $table->string('at_otrocertificado',200)->comment('Otros certificados: Especifique.')->nullable();
            $table->unsignedBigInteger('at_impresocolor_id')->comment('Color de impreso en producto acuerdo tecnico.')->nullable();
            $table->foreign('at_impresocolor_id','fk_acuerdotecnicotempedit_impreso_color')->references('id')->on('color')->onDelete('restrict')->onUpdate('restrict');
            $table->float('at_formatofilm', 6, 2)->comment('Formato film Strech')->nullable();
            $table->tinyInteger('at_aprobado')->comment('Estatus aprobar Acuerdo tecnico. 1=Aprobado 0=no fue aprobado. Al estar aprobado el siguiente paso es crear el producto y crear el acuerdo tecnico final en la tabla acuerdo tecnico y asociarlo al producto.')->nullable();
            $table->unsignedBigInteger('usuarioedit_id')->comment('Usuario Supervisor que editó el registro')->nullable();
            $table->foreign('usuarioedit_id','fk_acuerdotecnicotempedit_usuarioedit')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');

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
        Schema::dropIfExists('acuerdotecnicotempedit');
    }
}
