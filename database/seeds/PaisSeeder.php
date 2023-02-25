<?php

use Illuminate\Database\Seeder;

class PaisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('paises')->insert([
            'id' => 1,
            'nombre' => 'España',
        ]);
    }
}
