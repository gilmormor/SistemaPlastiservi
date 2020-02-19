<?php

use App\Models\Color;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TablaColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['nombre' => 'Azul',
            'descripcion' => 'Azul',
            'codcolor' => '#0080ff'
            ],
            ['nombre' => 'Rojo',
            'descripcion' => 'Rojo',
            'codcolor' => '#ff0000'
            ],
            ['nombre' => 'Amarillo',
            'descripcion' => 'Amarillo',
            'codcolor' => '#ffff00'
            ]
        ];
        foreach($datas as $data){
            Color::create([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'codcolor' => $data['codcolor'],
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
