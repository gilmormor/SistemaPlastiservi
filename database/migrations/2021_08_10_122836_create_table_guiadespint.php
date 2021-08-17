<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableGuiadespint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guiadespint', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->dateTime('fechahora')->comment('Fecha y hora de creacion registro');
            $table->unsignedBigInteger('clienteinterno_id');
            $table->foreign('clienteinterno_id','fk_guiadespint_clienteinterno')->references('id')->on('clienteinterno')->onDelete('restrict')->onUpdate('restrict');
            $table->string('cli_rut',10)->comment('RUT cliente');
            $table->string('cli_nom',80)->comment('Nombre cliente');
            $table->string('cli_dir',150)->comment('Direccion cliente');
            $table->string('cli_tel',20)->comment('Telefono cliente');
            $table->string('cli_giro',100)->comment('Giro cliente')->nullable();
            $table->string('observacion',200)->comment('Observaciones guia despacho interna')->nullable();
            $table->date('plazoentrega')->comment('Plazo de entrega fecha');
            $table->string('lugarentrega',100)->comment('Lugar de entrega');
            $table->unsignedBigInteger('comunaentrega_id')->comment('Comuna de entrega');
            $table->foreign('comunaentrega_id','fk_guiadespint_comunaentrega')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');
            $table->float('total',18,2)->comment('Total no incluye IVA');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario quien creo el registro');
            $table->foreign('usuario_id','fk_guiadespint_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->boolean('aprobstatus')->comment('Status de aprobacion (null o 0)=Sin aprobar por usuario quien creo la Guia interna, 1=Guia interna aprobada por usuario que la creo, 3=Guia interna aprobada por supervisor, 4=Rechazada por Supersivor')->nullable();
            $table->unsignedBigInteger('aprobusu_id')->comment('ID Usuario que aprobo o rechazo la Guia interna')->nullable();
            $table->foreign('aprobusu_id','fk_guiadespint_aprobusu')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('aprobfechahora')->comment('Fecha hora de aprobacion o rechazo')->nullable();
            $table->string('aprobobs',300)->comment('Observacion de aprovacion o rechazo')->nullable();
            $table->dateTime('anulada')->comment('Fecha hora de anulacion')->nullable();
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->unsignedBigInteger('sucursal_id')->comment("Id de Sucursal");
            $table->foreign('sucursal_id','fk_guiadespint_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('formapago_id');
            $table->foreign('formapago_id','fk_guiadespint_formapago')->references('id')->on('formapago')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('vendedor_id');
            $table->foreign('vendedor_id','fk_guiadespint_vendedor')->references('id')->on('vendedor')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('plazopago_id');
            $table->foreign('plazopago_id','fk_guiadespint_plazopago')->references('id')->on('plazopago')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('tipoentrega_id');
            $table->foreign('tipoentrega_id','fk_guiadespint_tipoentrega')->references('id')->on('tipoentrega')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('comuna_id');
            $table->foreign('comuna_id','fk_guiadespint_comuna')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');

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
        Schema::dropIfExists('guiadespint');
    }
}
