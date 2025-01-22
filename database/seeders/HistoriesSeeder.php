<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('historiales')->insert([
            [
                'cantidad' => 10,
                'id_bogeda_origen' => 1,
                'id_bodega_destino' => 2,
                'id_inventario' => 1,
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'cantidad' => 5,
                'id_bogeda_origen' => 2,
                'id_bodega_destino' => 1,
                'id_inventario' => 2,
                'created_by' => 2,
                'created_at' => now(),
            ],
        ]);
    }
}
