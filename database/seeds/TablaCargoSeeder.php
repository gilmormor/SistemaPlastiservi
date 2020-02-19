<?php

use App\Models\Cargo;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TablaCargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['nombre' => 'Gerente General',
            'descripcion' => 'Gerente General',
            'codcolor' => 'Gerente General'
            ],
            ['nombre' => 'Administradora',
            'descripcion' => 'Administradora',
            'codcolor' => 'Gerente General'
            ],
            ['nombre' => 'Contador',
            'descripcion' => 'Contador',
            'codcolor' => 'Gerente General'
            ],
            ['nombre' => 'Vendedor',
            'descripcion' => 'Vendedor'
            ],
            ['nombre' => 'Jefe de Informatica',
            'descripcion' => 'Jefe de Informatica'
            ],
            ['nombre' => 'Prevencionista de Riesgo',
            'descripcion' => 'Prevencionista de Riesgo'
            ],
            ['nombre' => 'SGI',
            'descripcion' => 'SGI'
            ]
        ];
        foreach($datas as $data){
            Cargo::create([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
