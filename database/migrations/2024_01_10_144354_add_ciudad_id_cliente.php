<?php

use App\Models\Cliente;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCiudadIdCliente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cliente', function (Blueprint $table) {
            $table->unsignedBigInteger('ciudad_id')->nullable()->comment('Id Ciudad')->nullable()->after('comunap_id');
            $table->foreign('ciudad_id','fk_cliente_ciudad')->references('id')->on('ciudad')->onDelete('restrict')->onUpdate('restrict');
        });
        $clientes = Cliente::orderBy('id')->get();
        foreach ($clientes as $cliente) {
            $cliente->ciudad_id = $cliente->provinciap_id;
            $cliente->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cliente', function (Blueprint $table) {
            $table->dropForeign('fk_cliente_ciudad');
            $table->dropColumn('ciudad_id');
        });
    }
}
