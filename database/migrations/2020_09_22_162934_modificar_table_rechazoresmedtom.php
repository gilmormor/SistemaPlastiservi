<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTableRechazoresmedtom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rechazoresmedtom', function (Blueprint $table) {
            $table->string('accorrec',250)->comment('Descripción Acción correctiva. Letra M')->nullable()->after('id');
            $table->dateTime('accorrecfec')->comment('Fecha Acción correctiva.')->nullable()->after('accorrec');
            $table->dateTime('fechacompromiso')->comment('Fecha de compromiso.')->nullable()->after('accorrecfec');
            $table->dateTime('fechacompromisofec')->comment('Fecha cuando se modifico la fecha de compromiso.')->nullable()->after('fechacompromiso');
            $table->dateTime('fechaguardado')->comment('Fecha hora en que se guarda o se envia.')->nullable()->after('fechacompromisofec');
            $table->integer('cumplimiento')->comment('1 = Cumplimiento, 0= Incumplimiento Esto lo hace el dueño de la noconformidad.')->nullable()->after('fechaguardado');
            $table->dateTime('fechacumplimiento')->comment('Fecha hora de cumplimiento.')->nullable()->after('cumplimiento')->after('cumplimiento');
            $table->integer('aprobpaso2')->comment('Aprobado por SGI Karen paso 2. 1=Aprobado, 0=Rechazado, null=sin valor. Si es rechazado debe generar Registro en la tabla RechazoNC=Rechazo no conformidad.')->nullable()->after('fechacumplimiento');
            $table->dateTime('fecaprobpaso2')->comment('Fecha hora aprobado paso 2 por SGI Karen.')->nullable()->after('aprobpaso2');
            $table->string('resmedtom',250)->comment('Resultado de las medidas tomadas.')->nullable()->after('fecaprobpaso2');
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
        Schema::table('rechazoresmedtom', function (Blueprint $table) {
            //
        });
    }
}
