<?php

use App\Models\ClienteDirec;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TablaClienteDirecSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['direccion' => 'Estacion Central',
            'cliente_id' => '1',
            'region_id' => '7',
            'provincia_id' => '25',
            'comuna_id' => '107',
            'formapago_id' => '1',
            'plazopago_id' => '3',
            'contactonombre' => 'Carlos Moreno',
            'contactoemail' => 'carlos@gmail.com',
            'contactotelef' => '253659845',
            'nombrefantasia' => 'CarlosArturo',
            'mostrarguiasfacturas' => '1',
            'finanzascontacto' => 'Arturo Moreno',
            'finanzanemail' => '11arturo@gmail.com',
            'finanzastelefono' => '321555214',
            'observaciones' => 'Observaciones CarlosArturo'
            ],
            ['direccion' => 'Santa Ester',
            'cliente_id' => '1',
            'region_id' => '7',
            'provincia_id' => '25',
            'comuna_id' => '105',
            'formapago_id' => '2',
            'plazopago_id' => '6',
            'contactonombre' => 'Eduardo Martinez',
            'contactoemail' => 'EduardoMartinez@gmail.com',
            'contactotelef' => '23652145214',
            'nombrefantasia' => 'EduardoMartinez',
            'mostrarguiasfacturas' => '0',
            'finanzascontacto' => 'Pedro Serrano',
            'finanzanemail' => 'PedroSerrano@gmail.com',
            'finanzastelefono' => '21536485447',
            'observaciones' => 'Observación EduardoMartinez 2'
            ],
            ['direccion' => 'Las Condes 2370',
            'cliente_id' => '2',
            'region_id' => '4',
            'provincia_id' => '8',
            'comuna_id' => '21',
            'formapago_id' => '2',
            'plazopago_id' => '6',
            'contactonombre' => 'David Moreno',
            'contactoemail' => 'alberto@gmail.com',
            'contactotelef' => '5452511451',
            'nombrefantasia' => 'David Ponque',
            'mostrarguiasfacturas' => '1',
            'finanzascontacto' => 'David Ponque1',
            'finanzanemail' => 'david>Pastelito@gmail.com',
            'finanzastelefono' => '2342423443',
            'observaciones' => 'Observaciones David Pastelito'
            ],
            ['direccion' => 'San Bernardo 4225',
            'cliente_id' => '2',
            'region_id' => '7',
            'provincia_id' => '25',
            'comuna_id' => '99',
            'formapago_id' => '3',
            'plazopago_id' => '3',
            'contactonombre' => 'Gilberto Moreno',
            'contactoemail' => 'gilbertomoreno@gmail.com',
            'contactotelef' => '2153654144',
            'nombrefantasia' => 'Gilberto Madera',
            'mostrarguiasfacturas' => '1',
            'finanzascontacto' => 'Maigialida Moreno',
            'finanzanemail' => 'Maiguallida@gmail.com',
            'finanzastelefono' => '23453445345',
            'observaciones' => 'Observaciones Maigualida'
            ],
            ['direccion' => 'Las cumbres',
            'cliente_id' => '3',
            'region_id' => '4',
            'provincia_id' => '9',
            'comuna_id' => '25',
            'formapago_id' => '2',
            'plazopago_id' => '3',
            'contactonombre' => 'Enrique',
            'contactoemail' => 'enrique@gmail.com',
            'contactotelef' => '321356456465',
            'nombrefantasia' => 'EnriqueOtra',
            'mostrarguiasfacturas' => '0',
            'finanzascontacto' => 'Otra',
            'finanzanemail' => 'Otra@gmail.com',
            'finanzastelefono' => '321321656',
            'observaciones' => 'Observaciones Enrique'
            ],
            ['direccion' => 'Santa Ester 510',
            'cliente_id' => '4',
            'region_id' => '7',
            'provincia_id' => '25',
            'comuna_id' => '101',
            'formapago_id' => '1',
            'plazopago_id' => '4',
            'contactonombre' => 'Martina Navratilova',
            'contactoemail' => 'Martina@gmail.com',
            'contactotelef' => '365258958',
            'nombrefantasia' => 'MartinaNavratilova',
            'mostrarguiasfacturas' => '1',
            'finanzascontacto' => 'Nadal',
            'finanzanemail' => 'nadal@gmail.com',
            'finanzastelefono' => '125635581',
            'observaciones' => 'Observaciones Martina Navratolova'
            ],
            ['direccion' => 'San Nicolas',
            'cliente_id' => '4',
            'region_id' => '7',
            'provincia_id' => '25',
            'comuna_id' => '124',
            'formapago_id' => '1',
            'plazopago_id' => '1',
            'contactonombre' => 'Sonia Lopez',
            'contactoemail' => 'sonialopez@gmail.com',
            'contactotelef' => '3215654655',
            'nombrefantasia' => 'Sonia Mayra',
            'mostrarguiasfacturas' => '1',
            'finanzascontacto' => 'Mayra',
            'finanzanemail' => 'mayra@plastiservi.cl',
            'finanzastelefono' => '3256422441',
            'observaciones' => 'Observación SoniaMayra'
            ]
        ];
        foreach($datas as  $data){
            ClienteDirec::create([
                'direccion' => $data['direccion'],
                'cliente_id' => $data['cliente_id'],
                'region_id' => $data['region_id'],
                'provincia_id' => $data['provincia_id'],
                'comuna_id' => $data['comuna_id'],
                'formapago_id' => $data['formapago_id'],
                'plazopago_id' => $data['plazopago_id'],
                'contactonombre' => $data['contactonombre'],
                'contactoemail' => $data['contactoemail'],
                'contactotelef' => $data['contactotelef'],
                'nombrefantasia' => $data['nombrefantasia'],
                'mostrarguiasfacturas' => $data['mostrarguiasfacturas'],
                'finanzascontacto' => $data['finanzascontacto'],
                'finanzanemail' => $data['finanzanemail'],
                'finanzastelefono' => $data['finanzastelefono'],
                'observaciones' => $data['observaciones'],
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
