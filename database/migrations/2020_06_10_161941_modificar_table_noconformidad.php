<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTableNoconformidad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('noconformidad', function (Blueprint $table) {
            $table->dateTime('accioninmediatafec')->comment('Fecha Accion inmediata.')->after('accioninmediata')->nullable();
            $table->dateTime('analisisdecausafec')->comment('Fecha Análisis de causa.')->after('analisisdecausa')->nullable();
            $table->dateTime('accorrecfec')->comment('Fecha Acción correctiva.')->after('accorrec')->nullable();
            $table->unsignedBigInteger('usuario_idmp2')->comment('Usuario quien modifico la paso 2.')->after('usuario_id')->nullable();;
            $table->foreign('usuario_idmp2','fk_noconformidad_usuariomp2')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('fechacompromisofec')->comment('Fecha cuendo se modifico la fecha de compromiso.')->after('fechacompromiso')->nullable();
            $table->integer('stavalai')->comment('Estatus validacion Accion inmediata no conformidad. Validar si la NC es o no una NC real. 1=Aplica para no conformidad 2=no aplica para no conformidad.')->after('accioninmediatafec')->nullable();
            $table->string('obsvalai',250)->comment('Observacion validacion Accion inmediata no conformidad.')->after('stavalai')->nullable();
            $table->dateTime('fechavalai')->comment('Fecha de validacion Accion inmediata no conformidad.')->after('obsvalai')->nullable();
            $table->unsignedBigInteger('usuario_idvalai')->comment('Usuario que valido la accion inmediata. Ya sea aceptacion o rechazo.')->after('fechavalai')->nullable();
            $table->foreign('usuario_idvalai','fk_noconformidad_usuariovalai')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->boolean('notirecep')->comment('Estatus notificacion Nueva Recepcion No Conformidad.')->after('feccierreaccorr')->nullable();
            $table->boolean('notivalai')->comment('Estatus notificacion Nueva Validacion accion inmediata.')->after('notirecep')->nullable();
            $table->boolean('noticumpl')->comment('Estatus notificacion Nueva Cumplimiento No Conformidad.')->after('notivalai')->nullable();
            $table->boolean('notiresgi')->comment('Estatus notificacion Nueva Revision SGI No Conformidad.')->after('noticumpl')->nullable();
            $table->dateTime('fecharesmedtom')->comment('Fecha que fueron registrados los Resultados de las medidas tomadas')->nullable()->after('resmedtom');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('noconformidad', function (Blueprint $table) {
            //
        });
    }
}
