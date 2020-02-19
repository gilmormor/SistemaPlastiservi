<?php

use Illuminate\Database\Seeder;

class UsuarioAdministradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usuario = Usuario::create([
            'usuario' => 'admin',
            'nombre' => 'Administrador',
            'email' => 'rgt90@hotmail.com',
            'password' => 'pass123'
        ]);
        $usuario->roles()->sync(1);

        $usuario = Usuario::create([
            'usuario' => 'rat',
            'nombre' => 'Roosvelt',
            'email' => 'rat@plastiservi.cl',
            'password' => 'pass123'
        ]);
        $usuario->roles()->sync(2);

    }
}
