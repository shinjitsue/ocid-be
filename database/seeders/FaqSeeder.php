<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Faq::insert([
            [
                'question' => 'What is OCID?',
                'answer' => 'OCID stands for the Office of Curriculum and Instruction Development. It is a unit under CSU that helps develop innovative university programs.'
            ],
            [
                'question' => 'How do I download forms?',
                'answer' => 'You can download forms by visiting the "Downloadables" section of the OCID website.'
            ],
            [
                'question' => 'Where is OCID located?',
                'answer' => 'OCID is located at Caraga State University - Main Campus, Ampayon, Butuan City.'
            ],
            [
                'question' => 'How can I contact OCID?',
                'answer' => 'You may contact OCID through the official CSU website or visit the office during working hours.'
            ],
            [
                'question' => 'What are the core values of CSU?',
                'answer' => 'The core values of CSU are Competence, Service, and Uprightness.'
            ],
            [
                'question' => 'What is the mission of CSU?',
                'answer' => 'The mission of CSU is to be a transformative university producing value-driven professionals and leaders for regional and national development.'
            ]
        ]);
    }
}
