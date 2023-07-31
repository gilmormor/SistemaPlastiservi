<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMonedaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moneda', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre',40);
            $table->string('desc',40);
            $table->string('simbolo',5);
            $table->float('valor',8,2)->comment('Valor moneda con respecto a moneda local');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario creó el registro');
            $table->foreign('usuario_id','fk_moneda_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
            $table->engine = 'InnoDB';
        });
        // Insertar valores iniciales
        $aux_fh = date("Y-m-d H:i:s");
        DB::table('moneda')->insert([
            ['nombre' => 'Peso', 'desc' => 'CL', 'simbolo' => '$', 'valor' => 1, 'usuario_id' => 1, 'created_at' => $aux_fh, 'updated_at' => $aux_fh],
            ['nombre' => 'Dolar', 'desc' => 'USD', 'simbolo' => '$', 'valor' => 827.84, 'usuario_id' => 1, 'created_at' => $aux_fh, 'updated_at' => $aux_fh]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moneda');
    }
}
