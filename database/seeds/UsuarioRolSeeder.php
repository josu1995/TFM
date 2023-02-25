<?php

use Illuminate\Database\Seeder;

class UsuarioRolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Roles para usuario test
        DB::table('rol_usuario')->insert([
            'rol_id' => 1,
            'usuario_id' => 1,
        ]);
        DB::table('rol_usuario')->insert([
            'rol_id' => 2,
            'usuario_id' => 1,
        ]);
        DB::table('rol_usuario')->insert([
            'rol_id' => 3,
            'usuario_id' => 1,
        ]);
        DB::table('rol_usuario')->insert([
            'rol_id' => 4,
            'usuario_id' => 1,
        ]);
        DB::table('rol_usuario')->insert([
            'rol_id' => 5,
            'usuario_id' => 1,
        ]);
        DB::table('rol_usuario')->insert([
            'rol_id' => 6,
            'usuario_id' => 1,
        ]);
        DB::table('rol_usuario')->insert([
            'rol_id' => 7,
            'usuario_id' => 1,
        ]);
        DB::table('rol_usuario')->insert([
            'rol_id' => 8,
            'usuario_id' => 1,
        ]);
        DB::table('rol_usuario')->insert([
            'rol_id' => 9,
            'usuario_id' => 1,
        ]);

        // Roles para usuario punto Bilbao
        DB::table('rol_usuario')->insert([
            'rol_id' => 2,
            'usuario_id' => 2,
        ]);
        DB::table('rol_usuario')->insert([
            'rol_id' => 4,
            'usuario_id' => 2,
        ]);

        // Roles para usuario punto Alicante
        DB::table('rol_usuario')->insert([
            'rol_id' => 2,
            'usuario_id' => 3,
        ]);
        DB::table('rol_usuario')->insert([
            'rol_id' => 4,
            'usuario_id' => 3,
        ]);

        // Roles para usuario punto Madrid
        DB::table('rol_usuario')->insert([
            'rol_id' => 2,
            'usuario_id' => 4,
        ]);
        DB::table('rol_usuario')->insert([
            'rol_id' => 4,
            'usuario_id' => 4,
        ]);

    }
}
