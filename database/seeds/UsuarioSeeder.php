<?php

use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Usuario test
        DB::table('usuarios')->insert([
            'id' => 1,
            'usuario' => '',
            'email' => 'test@transporter.es',
            'identificador' => str_random(30),
            'password' => bcrypt('testr'),
        ]);

        // Usuario punto Bilbao
        DB::table('usuarios')->insert([
            'id' => 2,
            'usuario' => '',
            'email' => 'bilbao@transporter.es',
            'identificador' => str_random(30),
            'password' => bcrypt('bilbaotr'),
        ]);

        // Usuario punto Alicante
        DB::table('usuarios')->insert([
            'id' => 3,
            'usuario' => '',
            'email' => 'alicante@transporter.es',
            'identificador' => str_random(30),
            'password' => bcrypt('alicantetr'),
        ]);

        // Usuario punto Madrid
        DB::table('usuarios')->insert([
            'id' => 4,
            'usuario' => '',
            'email' => 'madrid@transporter.es',
            'identificador' => str_random(30),
            'password' => bcrypt('madridtr'),
        ]);
    }
}
