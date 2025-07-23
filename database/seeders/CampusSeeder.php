<?php

namespace Database\Seeders;

use App\Models\Campus;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campuses = [
            [
                'name' => 'Caraga State University - Main Campus',
                'acronym' => 'CSU-MAIN',
                'address' => 'Ampayon, Butuan City, Agusan del Norte, Philippines',
            ],
            [
                'name' => 'Caraga State University - Cabadbaran Campus',
                'acronym' => 'CSU-CC',
                'address' => 'Cabadbaran City, Agusan del Norte, Philippines',
            ],
        ];

        foreach ($campuses as $campus) {
            Campus::firstOrCreate(
                ['acronym' => $campus['acronym']],
                $campus
            );
        }
    }
}
