<?php

use App\Models\Comuna;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCiudadIdComuna extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comuna', function (Blueprint $table) {
            $table->unsignedBigInteger('ciudad_id')->nullable()->comment('Id Ciudad')->nullable()->after('provincia_id');
            $table->foreign('ciudad_id','fk_comuna_ciudad')->references('id')->on('ciudad')->onDelete('restrict')->onUpdate('restrict');
        });
        $comunas = Comuna::orderBy('id')->get();
        foreach ($comunas as $comuna) {
            $comuna->ciudad_id = $comuna->provincia_id;
            $comuna->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comuna', function (Blueprint $table) {
            $table->dropForeign('fk_comuna_ciudad');
            $table->dropColumn('ciudad_id');
        });
    }
}
