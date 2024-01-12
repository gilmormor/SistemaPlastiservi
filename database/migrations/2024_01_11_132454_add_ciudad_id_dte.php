<?php

use App\Models\Dte;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCiudadIdDte extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dte', function (Blueprint $table) {
            $table->unsignedBigInteger('ciudad_id')->nullable()->comment('Id Ciudad')->nullable()->after('comuna_id');
            $table->foreign('ciudad_id','fk_dte_ciudad')->references('id')->on('ciudad')->onDelete('restrict')->onUpdate('restrict');
        });
        $dtes = Dte::orderBy('id')->get();
        foreach ($dtes as $dte) {
            $dte->ciudad_id = $dte->comuna->ciudad_id;
            $dte->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dte', function (Blueprint $table) {
            $table->dropForeign('fk_dte_ciudad');
            $table->dropColumn('ciudad_id');
        });
    }
}
