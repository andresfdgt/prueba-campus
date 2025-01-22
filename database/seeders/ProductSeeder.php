<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('productos')->insert([
            [
                'nombre' => 'Tarjeta Gráfica NVIDIA GeForce RTX 3080',
                'descripcion' => 'Tarjeta gráfica de alto rendimiento para juegos y diseño gráfico, modelo NVIDIA GeForce RTX 3080.',
                'estado' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Procesador Intel Core i9-11900K',
                'descripcion' => 'Procesador de última generación con múltiples núcleos, modelo Intel Core i9-11900K.',
                'estado' => 1,
                'created_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Memoria RAM Corsair Vengeance LPX 16GB',
                'descripcion' => 'Memoria RAM DDR4 de alta velocidad, modelo Corsair Vengeance LPX 16GB.',
                'estado' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Disco Duro SSD Samsung 970 EVO 1TB',
                'descripcion' => 'Disco duro SSD de 1TB para almacenamiento rápido, modelo Samsung 970 EVO 1TB.',
                'estado' => 1,
                'created_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Placa Base ASUS ROG Strix Z590-E',
                'descripcion' => 'Placa base compatible con los últimos procesadores y tarjetas gráficas, modelo ASUS ROG Strix Z590-E.',
                'estado' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
