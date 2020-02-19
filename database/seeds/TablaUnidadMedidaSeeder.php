<?php

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TablaUnidadMedidaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['nombre' => 'Cm.',
            'descripcion' => 'Centimetros',
            ],
            ['nombre' => 'Mc.',
            'descripcion' => 'Micras'
            ],
            ['nombre' => 'Pulgadas',
            'descripcion' => 'Pulgadas'
            ]
        ];
        foreach($datas as $data){
            UnidadMedida::create([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
