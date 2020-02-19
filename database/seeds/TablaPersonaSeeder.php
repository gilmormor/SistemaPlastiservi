<?php

use App\Models\Persona;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TablaPersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['nombre' => 'Carmen',
            'apellido' => 'Ramirez',
            'direccion' => 'San Miguel',
            'telefono' => '+5636598668',
            'ext' => '10',
            'email' => 'carmen@gmail.com',
            'cargo_id' => '4',
            'usuario_id' => '2'
            ],
            ['nombre' => 'Mari Carmen',
            'apellido' => 'Guerrero',
            'direccion' => 'Coronel Souper 4222',
            'telefono' => '+56968955',
            'ext' => '11',
            'email' => 'gilmormor@gmail.com',
            'cargo_id' => '1',
            'usuario_id' => '4'
            ],
            ['nombre' => 'Cristian',
            'apellido' => 'Gorigoitia',
            'direccion' => 'San Nicolas',
            'telefono' => '+565869855',
            'ext' => '12',
            'email' => 'cgorioitia@gmail.com',
            'cargo_id' => '2',
            'usuario_id' => NULL
            ],
            ['nombre' => 'Gilmer',
            'apellido' => 'Moreno',
            'direccion' => 'Coronel Souper 4222',
            'telefono' => '+56950824963',
            'ext' => '14',
            'email' => 'gmoreno@plastiservi.cl',
            'cargo_id' => '5',
            'usuario_id' => '1'
            ]
        ];
        foreach($datas as  $data){
            Persona::create([
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'direccion' => $data['direccion'],
                'telefono' => $data['telefono'],
                'ext' => $data['ext'],
                'email' => $data['email'],
                'cargo_id' => $data['cargo_id'],
                'usuario_id' => $data['usuario_id'],
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
