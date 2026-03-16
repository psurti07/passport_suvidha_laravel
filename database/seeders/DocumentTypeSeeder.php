<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documents = [
            [
                'name' => 'Original Proof of Date of Birth',
                'description' => 'Birth Certificate/SSC Certificate/Other accepted documents',
                'is_mandatory' => true
            ],
            [
                'name' => 'Address Proof',
                'description' => 'Aadhar Card/Voter ID/Utility Bills (not older than 3 months)',
                'is_mandatory' => true
            ],
            [
                'name' => 'Photo Identity Proof',
                'description' => 'Aadhar Card/PAN Card/Driving License',
                'is_mandatory' => true
            ],
            [
                'name' => 'Passport Size Photographs',
                'description' => '4 recent color photographs with white background (Size: 4.5cm x 3.5cm)',
                'is_mandatory' => true
            ],
            [
                'name' => 'Application Reference Number',
                'description' => 'Printout of application submission confirmation page',
                'is_mandatory' => true
            ]
        ];

        foreach ($documents as $document) {
            DocumentType::updateOrCreate(
                ['name' => $document['name']],
                $document
            );
        }
    }
} 