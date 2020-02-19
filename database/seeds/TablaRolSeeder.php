<?php

use Illuminate\Database\Seeder;
use App\Models\Admin\Rol;
use Carbon\Carbon;

class TablaRolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $rols = [
            'administrador',
            'editor',
            'supervisor'
        ];
        foreach($rols as  $key => $value){
            Rol::create([
                'nombre' => $value,
                'create_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
