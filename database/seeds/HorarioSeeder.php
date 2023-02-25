<?php

use Illuminate\Database\Seeder;

class HorarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Punto Bilbao Test
        DB::table('horarios')->insert([
            'id' => 1,
            'dia' => 1,
            'inicio' => '08:00:00',
            'fin' => '14:00:00',
            'cerrado' => 0,
            'punto_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 2,
            'dia' => 1,
            'inicio' => '15:00:00',
            'fin' => '20:00:00',
            'cerrado' => 0,
            'punto_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 3,
            'dia' => 2,
            'inicio' => '08:00:00',
            'fin' => '14:00:00',
            'cerrado' => 0,
            'punto_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 4,
            'dia' => 2,
            'inicio' => '15:00:00',
            'fin' => '20:00:00',
            'cerrado' => 0,
            'punto_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 5,
            'dia' => 3,
            'inicio' => '08:00:00',
            'fin' => '14:00:00',
            'cerrado' => 0,
            'punto_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 6,
            'dia' => 3,
            'inicio' => '15:00:00',
            'fin' => '20:00:00',
            'cerrado' => 0,
            'punto_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 7,
            'dia' => 4,
            'inicio' => '08:00:00',
            'fin' => '14:00:00',
            'cerrado' => 0,
            'punto_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 8,
            'dia' => 4,
            'inicio' => '15:00:00',
            'fin' => '20:00:00',
            'cerrado' => 0,
            'punto_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 9,
            'dia' => 5,
            'inicio' => '08:00:00',
            'fin' => '14:00:00',
            'cerrado' => 0,
            'punto_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 10,
            'dia' => 5,
            'inicio' => '15:00:00',
            'fin' => '20:00:00',
            'cerrado' => 0,
            'punto_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 11,
            'dia' => 6,
            'inicio' => '08:00:00',
            'fin' => '14:00:00',
            'cerrado' => 0,
            'punto_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 12,
            'dia' => 6,
            'inicio' => '15:00:00',
            'fin' => '20:00:00',
            'cerrado' => 0,
            'punto_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 13,
            'dia' => 7,
            'inicio' => '10:00:00',
            'fin' => '14:00:00',
            'cerrado' => 0,
            'punto_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 14,
            'dia' => 7,
            'inicio' => null,
            'fin' => null,
            'cerrado' => 1,
            'punto_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        
        // Punto Alicante Test
        DB::table('horarios')->insert([
            'id' => 15,
            'dia' => 1,
            'inicio' => '08:00:00',
            'fin' => '14:00:00',
            'cerrado' => 0,
            'punto_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 16,
            'dia' => 1,
            'inicio' => '15:00:00',
            'fin' => '20:00:00',
            'cerrado' => 0,
            'punto_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 17,
            'dia' => 2,
            'inicio' => '08:00:00',
            'fin' => '14:00:00',
            'cerrado' => 0,
            'punto_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 18,
            'dia' => 2,
            'inicio' => '15:00:00',
            'fin' => '20:00:00',
            'cerrado' => 0,
            'punto_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 19,
            'dia' => 3,
            'inicio' => '08:00:00',
            'fin' => '14:00:00',
            'cerrado' => 0,
            'punto_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 20,
            'dia' => 3,
            'inicio' => '15:00:00',
            'fin' => '20:00:00',
            'cerrado' => 0,
            'punto_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 21,
            'dia' => 4,
            'inicio' => '08:00:00',
            'fin' => '14:00:00',
            'cerrado' => 0,
            'punto_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 22,
            'dia' => 4,
            'inicio' => '15:00:00',
            'fin' => '20:00:00',
            'cerrado' => 0,
            'punto_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 23,
            'dia' => 5,
            'inicio' => '08:00:00',
            'fin' => '14:00:00',
            'cerrado' => 0,
            'punto_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 24,
            'dia' => 5,
            'inicio' => '15:00:00',
            'fin' => '20:00:00',
            'cerrado' => 0,
            'punto_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 25,
            'dia' => 6,
            'inicio' => '08:00:00',
            'fin' => '14:00:00',
            'cerrado' => 0,
            'punto_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 26,
            'dia' => 6,
            'inicio' => '15:00:00',
            'fin' => '20:00:00',
            'cerrado' => 0,
            'punto_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 27,
            'dia' => 7,
            'inicio' => '10:00:00',
            'fin' => '14:00:00',
            'cerrado' => 0,
            'punto_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('horarios')->insert([
            'id' => 28,
            'dia' => 7,
            'inicio' => null,
            'fin' => null,
            'cerrado' => 1,
            'punto_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }
}
