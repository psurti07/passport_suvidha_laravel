<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $customers;

    public function __construct(Collection $customers)
    {
        $this->customers = $customers;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->customers;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Define the header row
        return [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Mobile Number',
            'Status',
            'Created At',
            // Add other relevant customer fields if needed
            'Pack Code',
            'Address',
            'Gender',
            'Date of Birth',
            'Place of Birth',
            'Nationality',
            'Service Code',
        ];
    }

    /**
     * @param mixed $customer
     * @return array
     */
    public function map($customer): array
    {
        // Map customer data to columns
        return [
            $customer->id,
            $customer->first_name,
            $customer->last_name,
            $customer->email,
            $customer->mobile_number,
            $customer->is_paid ? 'Paid' : 'Lead', // Format status
            $customer->created_at->format('Y-m-d H:i:s'), // Format date
            // Add other fields corresponding to headings
            $customer->pack_code,
            $customer->address,
            $customer->gender,
            $customer->date_of_birth ? date('Y-m-d', strtotime($customer->date_of_birth)) : '', // Format date
            $customer->place_of_birth,
            $customer->nationality,
            $customer->service_code,
        ];
    }
} 