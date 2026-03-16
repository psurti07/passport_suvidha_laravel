<?php

namespace App\Exports;

use App\Models\Otp;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OtpsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $otps;

    public function __construct(Collection $otps)
    {
        $this->otps = $otps;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->otps;
    }

    /**
     * Define the headings for the export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Mobile Number',
            'OTP',
            'Sent At',
            'Status',
        ];
    }

    /**
     * Map the data for each row.
     *
     * @param mixed $otp
     * @return array
     */
    public function map($otp): array
    {
        return [
            $otp->id,
            $otp->mobile_number,
            $otp->otp,
            $otp->sent_at->format('Y-m-d H:i:s'),
            $otp->is_verified ? 'Verified' : 'Pending',
        ];
    }
}
