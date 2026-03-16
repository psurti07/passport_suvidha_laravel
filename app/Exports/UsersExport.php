<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $users;

    public function __construct($users = null)
    {
        $this->users = $users;
    }

    public function collection()
    {
        return $this->users ?? User::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Full Name',
            'Email Id',
            'Created At',
            'Updated At'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->created_at->format('d/m/Y'),
            $user->name,
            $user->email,
            $user->created_at->format('d/m/Y H:i:s'),
            $user->updated_at->format('d/m/Y H:i:s')
        ];
    }
} 