<?php

use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Configuracion para usuario test
        DB::table('configuraciones')->insert([
            'nombre' => 'Test',
            'apellidos' => 'Transporter',
            'ciudad' => 'Bilbao',
            'fecha_nacimiento' => \Carbon\Carbon::now(),
            'telefono' => '612345678',
            'dni' => '11111111H',
            'movil_certificado' => 1,
            'mail_certificado' => 1,
            'usuario_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        // Configuracion para usuario punto Bilbao
        DB::table('configuraciones')->insert([
            'nombre' => 'Bilbao',
            'apellidos' => 'Store',
            'ciudad' => 'Bilbao',
            'fecha_nacimiento' => \Carbon\Carbon::now(),
            'telefono' => '612345678',
            'dni' => '11111111H',
            'movil_certificado' => 1,
            'mail_certificado' => 1,
            'usuario_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        // Configuracion para punto Alicante
        DB::table('configuraciones')->insert([
            'nombre' => 'Alicante',
            'apellidos' => 'Store',
            'ciudad' => 'Alicante',
            'fecha_nacimiento' => \Carbon\Carbon::now(),
            'telefono' => '612345678',
            'dni' => '11111111H',
            'movil_certificado' => 1,
            'mail_certificado' => 1,
            'usuario_id' => 3,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        // Configuracion para punto Madrid
        DB::table('configuraciones')->insert([
            'nombre' => 'Madrid',
            'apellidos' => 'Store',
            'ciudad' => 'Madrid',
            'fecha_nacimiento' => \Carbon\Carbon::now(),
            'telefono' => '612345678',
            'dni' => '11111111H',
            'movil_certificado' => 1,
            'mail_certificado' => 1,
            'usuario_id' => 4,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

    }
}
