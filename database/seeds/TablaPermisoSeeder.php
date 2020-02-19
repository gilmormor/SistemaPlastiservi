<?php

use Illuminate\Database\Seeder;
use App\Models\admin\Permiso;

class TablaPermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Permiso::class,50)->create();
        //factory(Permiso::class)->times(50)->create();
    }
}
