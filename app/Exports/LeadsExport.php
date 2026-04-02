<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $leads;

    public function __construct(Collection $leads)
    {
        $this->leads = $leads;
    }

    /**
    * Return collection of leads
    */
    public function collection()
    {
        return $this->leads;
    }

    /**
     * Excel headings
     */
    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Mobile Number',
            'Status',
            'Created At',
            'Pack Code',
            'Address',
            'Gender',
            'Date of Birth',
            'Place of Birth',
            'Nationality',
            'Service Code',
            'Passport Type'
        ];
    }

    /**
     * Map database fields to excel columns
     */
    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->first_name,
            $lead->last_name,
            $lead->email,
            $lead->mobile_number,
            $lead->is_paid ? 'Paid' : 'Lead',
            $lead->created_at ? $lead->created_at->format('Y-m-d H:i:s') : '',
            $lead->pack_code,
            $lead->address,
            $lead->gender,
            $lead->date_of_birth ? date('Y-m-d', strtotime($lead->date_of_birth)) : '',
            $lead->place_of_birth,
            $lead->nationality,
            $lead->service_code,
            $lead->passport_type
        ];
    }
}