<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::firstOrCreate(
            ['slug' => 'demo-company'],
            [
                'name' => 'Empresa Demo',
                'plan' => 'pro',
            ]
        );
    }
}
