<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableClienteinterno extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clienteinterno', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('rut',12)->unique();
            $table->string('razonsocial',70)->comment('Razon Social')->unique();
            $table->string('direccion',200)->comment('Dirección');
            $table->string('telefono',50)->comment('Telefonos');
            $table->string('email',50)->comment('Correo electrónico');
            $table->unsignedBigInteger('comunap_id');
            $table->foreign('comunap_id','fk_clienteinterno_comuna')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('formapago_id');
            $table->foreign('formapago_id','fk_clienteinterno_formapago')->references('id')->on('formapago')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('plazopago_id');
            $table->foreign('plazopago_id','fk_clienteinterno_plazopago')->references('id')->on('plazopago')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('clienteinterno');
    }
}
