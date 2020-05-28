<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNoconformidad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('noconformidad', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario quien creo el registro.');
            $table->foreign('usuario_id','fk_noconformidad_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('motivonc_id')->comment('Id Motivo no conformidad.');
            $table->foreign('motivonc_id','fk_noconformidad_motivonc')->references('id')->on('motivonc')->onDelete('restrict')->onUpdate('restrict');
            $table->string('puntonormativo',250)->comment('Descripción punto normativo.')->nullable();
            $table->string('hallazgo',250)->comment('Descripción de la observación o hallazgo.');
            $table->unsignedBigInteger('formadeteccionnc_id')->comment('Id Motivo no conformidad.');
            $table->foreign('formadeteccionnc_id','fk_noconformidad_formadeteccionnc')->references('id')->on('formadeteccionnc')->onDelete('restrict')->onUpdate('restrict');
            $table->string('puntonorma',250)->comment('Descripción punto de la norma.')->nullable();
            $table->string('accioninmediata',250)->comment('Descripción Acción inmediata.')->nullable();
            $table->string('analisisdecausa',250)->comment('Descripción Análisis de causa.')->nullable();
            $table->string('accioncorrectiva',250)->comment('Descripción Acción correctiva.')->nullable();
            $table->date('fechacompromiso')->comment('Fecha de compromiso.')->nullable();
            $table->dateTime('fechaguardado')->comment('Fecha hora en que se guarda o se envia.')->nullable();
            $table->integer('cumplimiento')->comment('1 = Cumplimiento, 0= Incumplimiento Esto lo hace el dueño de la noconformidad.')->nullable();
            $table->dateTime('fechacumplimiento')->comment('Fecha hora de cumplimiento.')->nullable();
            $table->integer('aprobado')->comment('Aprobado por SGI Karen. 1=Aprobado, 0=Rechazado, null=sin valor. Si es rechazado debe generar Registro en la tabla RechazoNC=Rechazo no conformidad.')->nullable();
            $table->dateTime('fechaaprobado')->comment('Fecha hora aprobado por SGI Karen.')->nullable();
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro.')->nullable();
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
        Schema::dropIfExists('noconformidad');
    }
}
