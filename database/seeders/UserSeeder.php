<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'nombre' => 'Juan Pérez',
                'foto' => 'https://images.generated.photos/jxkfdgqTWF0xAfw1jTvvuZWI_LzIi6008riTNlUoTHc/rs:fit:512:512/wm:0.95:sowe:18:18:0.33/czM6Ly9pY29uczgu/Z3Bob3Rvcy1wcm9k/LnBob3Rvcy92M18w/NTkzNTE4LmpwZw.jpg',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'María Rodríguez',
                'foto' => 'https://images.generated.photos/pgwOKaqyr0o7TWj8mT0JrkRfSt0MmlCAlrd5qQvhakY/rs:fit:512:512/wm:0.95:sowe:18:18:0.33/czM6Ly9pY29uczgu/Z3Bob3Rvcy1wcm9k/LnBob3Rvcy92M18w/NTc2NjM0LmpwZw.jpg',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
