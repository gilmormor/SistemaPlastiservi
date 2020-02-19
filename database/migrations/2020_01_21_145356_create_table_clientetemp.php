<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableClientetemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientetemp', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('rut',12)->unique();
            $table->string('razonsocial',70)->unique();
            $table->string('direccion',200)->comment('Dirección');
            $table->string('telefono',50);
            $table->string('email',50);
            $table->unsignedBigInteger('vendedor_id')->nullable();
            $table->foreign('vendedor_id','fk_clientetemp_vendedor')->references('id')->on('vendedor')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('giro_id');
            $table->foreign('giro_id','fk_clientetemp_giro')->references('id')->on('giro')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('comunap_id');
            $table->foreign('comunap_id','fk_clientetemp_comuna')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');

            $table->unsignedBigInteger('formapago_id');
            $table->foreign('formapago_id','fk_clientetemp_formapago')->references('id')->on('formapago')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('plazopago_id');
            $table->foreign('plazopago_id','fk_clientetemp_plazopago')->references('id')->on('plazopago')->onDelete('restrict')->onUpdate('restrict');
            $table->string('contactonombre',50);
            $table->string('contactoemail',50);
            $table->string('contactotelef',50);
            $table->string('finanzascontacto',50);
            $table->string('finanzanemail',50);
            $table->string('finanzastelefono',50);
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->foreign('sucursal_id','fk_clientetemp_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');

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
        Schema::dropIfExists('clientetemp');
    }
}
