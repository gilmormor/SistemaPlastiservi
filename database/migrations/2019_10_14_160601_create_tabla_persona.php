<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablaPersona extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persona', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rut',14)->comment('RUT de la persona.');
            $table->string('nombre',60);
            $table->string('apellido',60);
            $table->string('direccion',100);
            $table->string('telefono',100);
            $table->string('ext',10);
            $table->string('email',100);
            $table->unsignedBigInteger('cargo_id');
            $table->foreign('cargo_id','fk_persona_cargo')->references('id')->on('cargo')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuario_id')->nullable()->comment('Id de Usuario. Puede quedar vacio.');
            $table->foreign('usuario_id','fk_persona_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('persona');
    }
}
