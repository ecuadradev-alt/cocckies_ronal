<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\Company;
use Carbon\Carbon;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('slug', 'demo-company')->first();

        News::create([
            'company_id' => $company->id,
            'titulo' => 'Lanzamiento de la nueva aplicación móvil',
            'descripcion' => 'Nueva app con múltiples funcionalidades.',
            'url' => 'https://ejemplo.com/nueva-app',
            'fecha_publicacion' => Carbon::now()->subDays(1),
        ]);
    }
}
