<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MetaKeyword;

class MetaKeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $metaKeywords = [
            [
                'slug' => 'home',
                'title' => 'Passport Assistance Services in India | New, Renewal & Tatkal Passport | PassportSuvidha',
                'descriptions' => 'Get expert assistance for new passport application, passport renewal, Tatkal passport, appointment booking and document guidance across India with PassportSuvidha.',
                'keywords' => 'passport assistance, passport renewal, tatkal passport, passport seva, passport application India, passport support',
            ],
            [
                'slug' => 'services',
                'title' => 'Passport Services in India | New Passport, Renewal & Tatkal Assistance',
                'descriptions' => 'Explore passport assistance services including new passport application, passport renewal, Tatkal passport support, document verification and appointment guidance across India.',
                'keywords' => 'passport services india, tatkal passport assistance, passport renewal services, new passport support, passport document verification, passport appointment assistance',
            ],
            
        ];

        foreach ($metaKeywords as $metaKeyword) {
            MetaKeyword::updateOrCreate(
                ['slug' => $metaKeyword['slug']],
                $metaKeyword
            );
        }
    }
}
