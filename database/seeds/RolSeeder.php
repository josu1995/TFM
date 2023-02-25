<?php

use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('roles')->insert([
            'id' => 1,
            'tipo' => 'administrador',
            'descripcion' => 'Administrador de la plataforma',
        ]);
        DB::table('roles')->insert([
            'id' => 2,
            'tipo' => 'usuario',
            'descripcion' => 'Usuario cliente de servicio',
        ]);
        DB::table('roles')->insert([
            'id' => 3,
            'tipo' => 'transportista',
            'descripcion' => 'Usuario de tipo transportista',
        ]);
        DB::table('roles')->insert([
            'id' => 4,
            'tipo' => 'punto',
            'descripcion' => 'Gestión de punto de recogida/envío',
        ]);
        DB::table('roles')->insert([
            'id' => 5,
            'tipo' => 'blog',
            'descripcion' => 'Administrador del blog',
        ]);
        DB::table('roles')->insert([
            'id' => 6,
            'tipo' => 'cliente',
            'descripcion' => 'Cliente que realiza envíos',
        ]);
        DB::table('roles')->insert([
            'id' => 7,
            'tipo' => 'cliente_potencial',
            'descripcion' => 'Cliente potencial',
        ]);
        DB::table('roles')->insert([
            'id' => 8,
            'tipo' => 'transportista_potencial',
            'descripcion' => 'Transportista potencial',
        ]);
        DB::table('roles')->insert([
            'id' => 9,
            'tipo' => 'profesional',
            'descripcion' => 'Profesional',
        ]);
    }
}
