<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        $this->call(TiposRecogidasAlmacenesSeeder::class);

        // Datos dev
//        if(env('APP_ENV') == 'testing') {
//
//            // Statics
//            $this->call('RolSeeder');
//            $this->call('CoberturaSeeder');
//            $this->call('BancoSeeder');
//            $this->call('EmbalajeSeeder');
//            $this->call('EstadoSeeder');
//            $this->call('EstadosPagoSeeder');
//            $this->call('EstadoViajeSeeder');
//            $this->call('MetodosPagoSeeder');
//            $this->call('OpcionSeeder');
//            $this->call('PaisSeeder');
//            $this->call('ProvinciasSeeder');
//            $this->call('LocalidadSeeder');
//            $this->call('RangoSeeder');
//            $this->call('RutaSeeder');
//            $this->call('TipoAlertaSeeder');
//            $this->call('TipoTarjetaSeeder');
//
//            // Dynamics for testing
//            $this->call('UsuarioSeeder');
//            $this->call('PuntoSeeder');
//            $this->call('HorarioSeeder');
//            $this->call('ConfiguracionSeeder');
//            $this->call('StockSeeder');
//            $this->call('UsuarioRolSeeder');
//
//        } else if(env('APP_ENV') != 'produccion') {
//            // Statics
//            $this->call('RolSeeder');
//            $this->call('CoberturaSeeder');
//            $this->call('BancoSeeder');
//            $this->call('EmbalajeSeeder');
//            $this->call('EstadoSeeder');
//            $this->call('EstadosPagoSeeder');
//            $this->call('EstadoViajeSeeder');
//            $this->call('MetodosPagoSeeder');
//            $this->call('OpcionSeeder');
//            $this->call('PaisSeeder');
//            $this->call('ProvinciasSeeder');
//            $this->call('LocalidadSeeder');
//            $this->call('RangoSeeder');
//            $this->call('RutaSeeder');
//            $this->call('TipoAlertaSeeder');
//            $this->call('TipoTarjetaSeeder');
//        }
    }
}
