<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTableRechazonc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rechazonc', function (Blueprint $table) {
            $table->dateTime('accioninmediatafec')->comment('Fecha Accion inmediata.')->after('accioninmediata')->nullable();
            $table->dateTime('analisisdecausafec')->comment('Fecha Análisis de causa.')->after('analisiscausa')->nullable();
            $table->dateTime('accorrecfec')->comment('Fecha Acción correctiva.')->after('accioncorrectiva')->nullable();
            
            $table->dateTime('fechacompromiso')->comment('Fecha de compromiso.')->after('accorrecfec')->nullable();
            $table->dateTime('fechacompromisofec')->comment('Fecha cuendo se modifico la fecha de compromiso.')->after('fechacompromiso')->nullable();
            $table->dateTime('fechaguardado')->comment('Fecha hora en que se guarda o se envia.')->after('fechacompromisofec')->nullable();

            $table->integer('cumplimiento')->comment('1 = Cumplimiento, 0= Incumplimiento Esto lo hace el dueño de la noconformidad.')->after('fechaguardado')->nullable();
            $table->dateTime('fechacumplimiento')->comment('Fecha hora de cumplimiento.')->after('cumplimiento')->nullable();

            $table->integer('aprobpaso2')->comment('Aprobado por SGI Karen paso 2. 1=Aprobado, 0=Rechazado, null=sin valor. Si es rechazado debe generar Registro en la tabla RechazoNC=Rechazo no conformidad.')->after('fechacumplimiento')->nullable();
            $table->dateTime('fecaprobpaso2')->comment('Fecha hora aprobado paso 2 por SGI Karen.')->after('aprobpaso2')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rechazonc', function (Blueprint $table) {
            //
        });
    }
}
