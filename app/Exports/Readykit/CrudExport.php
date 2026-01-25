<?php

namespace App\Exports\Readykit;

use App\Models\App\Crud\Crud;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CrudExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            __t('name'),
            __t('email'),
            __t('phone'),
            __t('gender'),
            __t('age')
        ];
    }

    public function collection()
    {
        $data = Crud::query()->latest()->get();

        return $data->map(function ($row) {
            return [
                'name' => $row->name,
                'email' => $row->email,
                'phone' => $row->phone,
                'gender' => $row->gender,
                'age' => $row->age
            ];
        });
    }
}
