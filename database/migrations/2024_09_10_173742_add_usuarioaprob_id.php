<?php

use App\Models\InvEntSal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUsuarioaprobId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventsal', function (Blueprint $table) {
            $table->unsignedBigInteger('usuarioaprob_id')->nullable()->after('staaprob')->comment('Id de usuario que aprobo el envio a invmov.');
            $table->foreign('usuarioaprob_id','fk_inventsal_usuarioaprob')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');

        });
        DB::table('inventsal')
        ->update([
            'usuarioaprob_id' => DB::raw('usuario_id'),
        ]);
        /* $inventsals = InvEntSal::orderBy('id')->get();
        foreach ($inventsals as $inventsal) {
            $inventsal->usuarioaprob_id = $inventsal->usuario_id;
            $inventsal->save();
        } */

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventsal', function (Blueprint $table) {
            $table->dropForeign('fk_inventsal_usuarioaprob');
            $table->dropColumn('usuarioaprob_id');
        });
    }
}
