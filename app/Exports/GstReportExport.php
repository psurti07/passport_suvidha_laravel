<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class GstReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Define the column headers for the export
        return [
            'INV Date',
            'INV #',
            'Net Amount',
            'CGST',
            'SGST',
            'IGST',
            'Total Amount',
            'Fullname',
            'Mobile',
            'Email',
            'GST No',
            'City',
            'State',
        ];
    }

    /**
     * @param mixed $item The data row
     * @return array
     */
    public function map($item): array
    {
        // Map the data from the collection item to the row array
        // Ensure keys match the actual keys/properties in your GstRecord model/collection items
        return [
            $item->inv_date ? Carbon::parse($item->inv_date)->format('d/m/Y') : '',
            $item->inv_no ?? '',
            number_format($item->net_amount ?? 0, 2, '.', ''), // Use . for decimal separator in Excel/CSV
            number_format($item->cgst ?? 0, 2, '.', ''),
            number_format($item->sgst ?? 0, 2, '.', ''),
            number_format($item->igst ?? 0, 2, '.', ''),
            number_format($item->total_amount ?? 0, 2, '.', ''),
            $item->fullname ?? '',
            $item->mobile ?? '',
            strtolower($item->email ?? ''),
            $item->gst_no ?? '',
            $item->city ?? '',
            $item->state ?? '',
        ];
    }
} 