<?php

use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('stocks')->insert([
            'store_id' => 2,
            'bolsas' => 20,
            'pegatinas' => 1000,
            'cintas' => 3
        ]);
        DB::table('stocks')->insert([
            'store_id' => 3,
            'bolsas' => 20,
            'pegatinas' => 1000,
            'cintas' => 3
        ]);
    }
}
