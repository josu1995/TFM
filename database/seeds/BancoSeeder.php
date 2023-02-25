<?php

use Illuminate\Database\Seeder;

class BancoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('bancos')->insert([
            'id' => '0003',
            'nombre' => 'BANCO DE DEPOSITOS'
        ]);
        DB::table('bancos')->insert([
            'id' => '0019',
            'nombre' => 'DEUTSCHE BANK S.A.E.'
        ]);
        DB::table('bancos')->insert([
            'id' => '0038',
            'nombre' => 'SANTANDER SECURITIES SERVICES'
        ]);
        DB::table('bancos')->insert([
            'id' => '0049',
            'nombre' => 'BANCO SANTANDER'
        ]);
        DB::table('bancos')->insert([
            'id' => '0057',
            'nombre' => 'BANCO DEPOSITARIO BBVA'
        ]);
        DB::table('bancos')->insert([
            'id' => '0061',
            'nombre' => 'BANCA MARCH'
        ]);
        DB::table('bancos')->insert([
            'id' => '0073',
            'nombre' => 'OPEN BANK S.A.'
        ]);
        DB::table('bancos')->insert([
            'id' => '0075',
            'nombre' => 'BANCO POPULAR ESPAÑOL'
        ]);
        DB::table('bancos')->insert([
            'id' => '0078',
            'nombre' => 'BANCA PUEYO'
        ]);
        DB::table('bancos')->insert([
            'id' => '0081',
            'nombre' => 'BANCO DE SABADELL'
        ]);
        DB::table('bancos')->insert([
            'id' => '0083',
            'nombre' => 'RENTA 4 BANCO'
        ]);
        DB::table('bancos')->insert([
            'id' => '0094',
            'nombre' => 'RBC INVESTOR SERVICES ESPAÑA'
        ]);
        DB::table('bancos')->insert([
            'id' => '0108',
            'nombre' => 'SOCIETE GENERALE SUCURSAL EN ESPAÑA'
        ]);
        DB::table('bancos')->insert([
            'id' => '0128',
            'nombre' => 'BANKINTER'
        ]);
        DB::table('bancos')->insert([
            'id' => '0130',
            'nombre' => 'BANCO CAIXA GERAL'
        ]);
        DB::table('bancos')->insert([
            'id' => '0131',
            'nombre' => 'NOVO BANCO SA SUCURSAL EN ESPAÑA'
        ]);
        DB::table('bancos')->insert([
            'id' => '0138',
            'nombre' => 'BANKOA'
        ]);
        DB::table('bancos')->insert([
            'id' => '0144',
            'nombre' => 'BNP PARIBAS SECURITIES SERVICES'
        ]);
        DB::table('bancos')->insert([
            'id' => '0152',
            'nombre' => 'BARCLAYS BANK PLC'
        ]);
        DB::table('bancos')->insert([
            'id' => '0182',
            'nombre' => 'BANCO BILBAO VIZCAYA ARGENTARIA'
        ]);
        DB::table('bancos')->insert([
            'id' => '0186',
            'nombre' => 'BANCO MEDIOLANUM'
        ]);
        DB::table('bancos')->insert([
            'id' => '0188',
            'nombre' => 'BANCO ALCALA'
        ]);
        DB::table('bancos')->insert([
            'id' => '0198',
            'nombre' => 'BANCO COOPERATIVO ESPAÑOL'
        ]);
        DB::table('bancos')->insert([
            'id' => '0211',
            'nombre' => 'EBN BANCO DE NEGOCIOS'
        ]);
        DB::table('bancos')->insert([
            'id' => '0224',
            'nombre' => 'SANTANDER CONSUMER FINANCE'
        ]);

    }
}
