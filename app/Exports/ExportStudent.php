<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportStudent implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::select('username', 'name', 'email', 'gender')->where('role', 'USER')->get();
    }

    public function headings(): array
    {
        return ["ID", "USERNAME", "EMAIL", 'GENDER'];
    }
}
