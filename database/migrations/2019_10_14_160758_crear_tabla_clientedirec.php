<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaClientedirec extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientedirec', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('direccion',150);
            $table->string('direcciondetalle',150);
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id','fk_clientedirec_cliente')->references('id')->on('cliente')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('region_id');
            $table->foreign('region_id','fk_clientedirec_region')->references('id')->on('region')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('provincia_id');
            $table->foreign('provincia_id','fk_clientedirec_provincia')->references('id')->on('provincia')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('comuna_id');
            $table->foreign('comuna_id','fk_clientedirec_comuna')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('formapago_id');
            $table->foreign('formapago_id','fk_clientedirec_formapago')->references('id')->on('formapago')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('plazopago_id');
            $table->foreign('plazopago_id','fk_clientedirec_plazopago')->references('id')->on('plazopago')->onDelete('restrict')->onUpdate('restrict');
            $table->string('contactonombre',50);
            $table->string('contactoemail',50);
            $table->string('contactotelef',50);
            $table->string('nombrefantasia',50)->nullable();
            $table->integer('mostrarguiasfacturas');
            $table->string('finanzascontacto',50);
            $table->string('finanzanemail',50);
            $table->string('finanzastelefono',50);
            $table->string('observaciones',200)->nullable();
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
        Schema::dropIfExists('clientedirec');
    }
}
