<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use Carbon\Carbon;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $newsData = [
            [
                'titulo' => 'Lanzamiento de la nueva aplicación móvil',
                'descripcion' => 'La empresa anunció el lanzamiento de su nueva app móvil con múltiples funcionalidades.',
                'url' => 'https://ejemplo.com/nueva-app',
                'fecha_publicacion' => Carbon::now()->subDays(1),
            ],
            [
                'titulo' => 'Conferencia tecnológica 2025',
                'descripcion' => 'Se llevará a cabo la conferencia anual de tecnología en Lima con expertos internacionales.',
                'url' => 'https://ejemplo.com/conferencia-2025',
                'fecha_publicacion' => Carbon::now()->subDays(5),
            ],
            [
                'titulo' => 'Actualización de políticas de privacidad',
                'descripcion' => 'La compañía actualizó sus políticas de privacidad para mejorar la transparencia con los usuarios.',
                'url' => 'https://ejemplo.com/politicas-privacidad',
                'fecha_publicacion' => Carbon::now()->subDays(10),
            ],
        ];

        foreach ($newsData as $item) {
            News::create($item);
        }
    }
}
