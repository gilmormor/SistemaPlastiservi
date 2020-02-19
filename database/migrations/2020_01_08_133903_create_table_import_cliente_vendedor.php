<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableImportClienteVendedor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('importclientevendedor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("cliente_rut",12)->comment('RUT del cliente');
            $table->unsignedBigInteger('vendedor_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('importclientevendedor');
    }
}
