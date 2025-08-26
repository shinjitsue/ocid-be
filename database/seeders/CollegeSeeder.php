<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Campus;
use App\Models\College;

class CollegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get campus IDs
        $csuMain = Campus::where('acronym', 'CSU-MAIN')->first();
        $csuCC = Campus::where('acronym', 'CSU-CC')->first();

        if (!$csuMain || !$csuCC) {
            $this->command->error('Campuses not found. Please run CampusSeeder first.');
            return;
        }

        $colleges = [
            // CSU-MAIN Colleges
            [
                'name' => 'College of Engineering and Information Technology',
                'acronym' => 'CEIT',
                'campus_id' => $csuMain->id,
            ],
            [
                'name' => 'College of Mathematics and Natural Sciences',
                'acronym' => 'CMNS',
                'campus_id' => $csuMain->id,
            ],
            [
                'name' => 'College of Teacher Education',
                'acronym' => 'CTE',
                'campus_id' => $csuMain->id,
            ],
            [
                'name' => 'College of Business and Management',
                'acronym' => 'CBM',
                'campus_id' => $csuMain->id,
            ],
            [
                'name' => 'College of Arts and Social Sciences',
                'acronym' => 'CASS',
                'campus_id' => $csuMain->id,
            ],
            [
                'name' => 'College of Agriculture and Natural Resources',
                'acronym' => 'CANR',
                'campus_id' => $csuMain->id,
            ],
            [
                'name' => 'College of Forestry and Environmental Sciences',
                'acronym' => 'CFES',
                'campus_id' => $csuMain->id,
            ],
            [
                'name' => 'College of Medicine',
                'acronym' => 'COM',
                'campus_id' => $csuMain->id,
            ],
            [
                'name' => 'College of Nursing',
                'acronym' => 'CON',
                'campus_id' => $csuMain->id,
            ],
            [
                'name' => 'College of Public Administration and Governance',
                'acronym' => 'CPAG',
                'campus_id' => $csuMain->id,
            ],
            [
                'name' => 'Graduate School',
                'acronym' => 'GS',
                'campus_id' => $csuMain->id,
            ],

            // CSU-CC Colleges
            [
                'name' => 'College of Teacher Education - Cabadbaran',
                'acronym' => 'CTE-CC',
                'campus_id' => $csuCC->id,
            ],
            [
                'name' => 'College of Business and Management - Cabadbaran',
                'acronym' => 'CBM-CC',
                'campus_id' => $csuCC->id,
            ],
            [
                'name' => 'College of Engineering and Information Technology - Cabadbaran',
                'acronym' => 'CEIT-CC',
                'campus_id' => $csuCC->id,
            ],
            [
                'name' => 'College of Arts and Social Sciences - Cabadbaran',
                'acronym' => 'CASS-CC',
                'campus_id' => $csuCC->id,
            ],
            [
                'name' => 'College of Agriculture and Natural Resources - Cabadbaran',
                'acronym' => 'CANR-CC',
                'campus_id' => $csuCC->id,
            ],
        ];

        foreach ($colleges as $college) {
            College::firstOrCreate(
                ['acronym' => $college['acronym']],
                $college
            );
        }

        $this->command->info('College seeder completed successfully!');
    }
}
