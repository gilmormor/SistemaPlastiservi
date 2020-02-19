<?php

use App\Models\Color;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    
        /*
        $this->truncateTablas([
            'rol',
            'permiso',
            'usuario',
            'usuario_rol'
        ]);
        // $this->call(UsersTableSeeder::class);
        $this->call(TablaRolSeeder::class);
        $this->call(TablaPermisoSeeder::class);
        $this->call(UsuarioAdministradorSeeder::class);
        */

        $this->truncateTablas([
            'formapago',
            'plazopago',
            'cliente',
            'clientedirec',
            'cargo',
            'color',
            'matfabr',
            'unidadmedida',
            'certificado',
            'persona',
            'vendedor',
            'jefatura_sucursal_area_persona'
        ]);
        // $this->call(UsersTableSeeder::class);
        $this->call(TablaFormapagoSeeder::class);
        $this->call(TablaPlazoPagoSeeder::class);
        $this->call(TablaClienteSeeder::class);
        $this->call(TablaClienteDirecSeeder::class);
        $this->call(TablaCargoSeeder::class);
        $this->call(TablaColorSeeder::class);
        $this->call(TablaMatFabrSeeder::class);
        $this->call(TablaUnidadMedidaSeeder::class);
        $this->call(TablaCertificadoSeeder::class);
        $this->call(TablaPersonaSeeder::class);
        $this->call(TablaVendedorSeeder::class);
        $this->call(TablaJefatura_sucursal_area_personaSeeder::class);
    }
    protected function truncateTablas(array $tablas){
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        foreach($tablas as $tabla){
            DB::table($tabla)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
