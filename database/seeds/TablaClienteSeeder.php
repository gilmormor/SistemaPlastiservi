<?php

use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TablaClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['rut' => '268177345',
            'razonsocial' => 'Gilmer Moreno'],
            ['rut' => '268177346',
            'razonsocial' => 'Maria Carolina Romero'],
            ['rut' => '26817737',
            'razonsocial' => 'Prueba'],
            ['rut' => '287272776',
            'razonsocial' => 'Plastiservi']
        ];
        foreach($datas as  $data){
            Cliente::create([
                'rut' => $data['rut'],
                'razonsocial' => $data['razonsocial'],
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
